SET SERVEROUTPUT ON
--Se agrega la parametrización del directorio usado para la subida de archivos de cambio de plan masivo
DECLARE
  Ln_IdParamDirPsm NUMBER(5,0);
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
      'DIRECTORIO_PSM_MD',
      'Directorio de Telcos donde se guardarán los archivos subidos para generación de policy y scopes',
      'Activo',
      'afayala',
      CURRENT_TIMESTAMP,
      '127.0.0.1'
    );
  SELECT ID_PARAMETRO
  INTO Ln_IdParamDirPsm
  FROM DB_GENERAL.ADMI_PARAMETRO_CAB
  WHERE NOMBRE_PARAMETRO='DIRECTORIO_PSM_MD'
  AND ESTADO            = 'Activo';
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
      Ln_IdParamDirPsm,
      'Valor1: Nombre de dir en la BD, Valor2: Ruta del dir en la BD, Valor3: Ruta del dir en Telcos',
      'DIR_PROCESOS_MASIVOS',
      '/app/telcos/reportes/MD/tecnico/procesos_masivos/',
      'telcos/web/public/uploads/MD/tecnico/procesos_masivos/',
      NULL,
      NULL,
      NULL,
      'Activo',
      'afayala',
      sysdate,
      '127.0.0.1',
      NULL,
      NULL,
      NULL,
      '18'
    );
  SYS.DBMS_OUTPUT.PUT_LINE('Se creó correctamente el directorio para los archivos csv subidos en Telcos para la generación masiva de policy y scopes');
  COMMIT;
EXCEPTION
WHEN OTHERS THEN
  SYS.DBMS_OUTPUT.PUT_LINE('Error: '|| SQLCODE || ' - ERROR_STACK: ' || DBMS_UTILITY.FORMAT_ERROR_STACK || ' - ERROR_BACKTRACE: ' 
                           || DBMS_UTILITY.FORMAT_ERROR_BACKTRACE);
  ROLLBACK;
END;
/
--Creación de nueva plantilla para el envío de correo con el consolidado del archivo procesado para el cambio de plan masivo
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
      'NOTIFICACIÓN AUTOMÁTICA CON EL DETALLE DE LOS ELEMENTOS POR GENERACIÓN MASIVA DE POLICY Y SCOPES',
      'PSMSUBIDA',
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
                        <td colspan="2">El presente correo es para indicarle que se procedió a subir al telcos un archivo 
                            con {{NUM_REGISTROS_TOTAL}} registros de generación masiva de policy y scopes.
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
                        <td colspan="2">Se adjunta el archivo con el detalle de los elementos procesados.</td>
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
  WHERE CODIGO='PSMSUBIDA';
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
      7,--jbozada@telconet.ec
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
      148,--red_acceso@netlife.net.ec
      Ln_IdPlantilla,
      'Activo',
      SYSDATE,
      'afayala',
      'NO'
    );
  SYS.DBMS_OUTPUT.PUT_LINE('Se creó la plantilla correctamente PSMSUBIDA');
  COMMIT;
EXCEPTION
WHEN OTHERS THEN
  SYS.DBMS_OUTPUT.PUT_LINE('Error: '|| SQLCODE || ' - ERROR_STACK: ' || DBMS_UTILITY.FORMAT_ERROR_STACK || ' - ERROR_BACKTRACE: ' 
                            || DBMS_UTILITY.FORMAT_ERROR_BACKTRACE);
  ROLLBACK;
END;
/


