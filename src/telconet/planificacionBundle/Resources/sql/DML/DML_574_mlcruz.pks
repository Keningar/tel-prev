SET SERVEROUTPUT ON
DECLARE
  Ln_IdParametroTiempoLiberar NUMBER;
  Ln_IdParametroAliasNotif    NUMBER;
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
      'TIEMPOS_Y_ALIAS_LIBERACION_FACTIBILIDAD',
      'Tiempos en días y alias designados por empresa, región y última milla para la liberación de recursos',
      'Activo',
      'mlcruz',
      CURRENT_TIMESTAMP,
      '127.0.0.1'
    );
  SELECT ID_PARAMETRO
  INTO Ln_IdParametroTiempoLiberar
  FROM DB_GENERAL.ADMI_PARAMETRO_CAB
  WHERE NOMBRE_PARAMETRO='TIEMPOS_Y_ALIAS_LIBERACION_FACTIBILIDAD';
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
      EMPRESA_COD,
      ESTADO,
      USR_CREACION,
      FE_CREACION,
      IP_CREACION
    )
    VALUES
    (
      DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
      Ln_IdParametroTiempoLiberar,
      'TN',
      'R1',
      'FO',
      '90',
      'gis_gye@telconet.ec,aventas_gye@telconet.ec,jefaturas_prov_r1@telconet.ec,ventas_provincias@telconet.ec,',
      '10',
      'Activo',
      'mlcruz',
      CURRENT_TIMESTAMP,
      '127.0.0.1'
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
      EMPRESA_COD,
      ESTADO,
      USR_CREACION,
      FE_CREACION,
      IP_CREACION
    )
    VALUES
    (
      DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
      Ln_IdParametroTiempoLiberar,
      'TN',
      'R2',
      'FO',
      '90',
      'gis_uio@telconet.ec,aventas_uio@telconet.ec,jefaturas_prov_r2@telconet.ec,ventas_provincias@telconet.ec,',
      '10',
      'Activo',
      'mlcruz',
      CURRENT_TIMESTAMP,
      '127.0.0.1'
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
      EMPRESA_COD,
      ESTADO,
      USR_CREACION,
      FE_CREACION,
      IP_CREACION
    )
    VALUES
    (
      DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
      Ln_IdParametroTiempoLiberar,
      'TN',
      'R1',
      'RAD',
      '90',
      'radioenlace_gye@telconet.ec,aventas_gye@telconet.ec,jefaturas_prov_r1@telconet.ec,ventas_provincias@telconet.ec,',
      '10',
      'Activo',
      'mlcruz',
      CURRENT_TIMESTAMP,
      '127.0.0.1'
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
      EMPRESA_COD,
      ESTADO,
      USR_CREACION,
      FE_CREACION,
      IP_CREACION
    )
    VALUES
    (
      DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
      Ln_IdParametroTiempoLiberar,
      'TN',
      'R2',
      'RAD',
      '90',
      'radioenlace_uio@telconet.ec,aventas_uio@telconet.ec,jefaturas_prov_r2@telconet.ec,ventas_provincias@telconet.ec,',
      '10',
      'Activo',
      'mlcruz',
      CURRENT_TIMESTAMP,
      '127.0.0.1'
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
      EMPRESA_COD,
      ESTADO,
      USR_CREACION,
      FE_CREACION,
      IP_CREACION
    )
    VALUES
    (
      DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
      Ln_IdParametroTiempoLiberar,
      'TN',
      'R1',
      'UTP',
      '90',
      'gis_gye@telconet.ec,aventas_gye@telconet.ec,jefaturas_prov_r1@telconet.ec,ventas_provincias@telconet.ec,',
      '10',
      'Activo',
      'mlcruz',
      CURRENT_TIMESTAMP,
      '127.0.0.1'
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
      EMPRESA_COD,
      ESTADO,
      USR_CREACION,
      FE_CREACION,
      IP_CREACION
    )
    VALUES
    (
      DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
      Ln_IdParametroTiempoLiberar,
      'TN',
      'R2',
      'UTP',
      '90',
      'gis_uio@telconet.ec,aventas_uio@telconet.ec,jefaturas_prov_r2@telconet.ec,ventas_provincias@telconet.ec,',
      '10',
      'Activo',
      'mlcruz',
      CURRENT_TIMESTAMP,
      '127.0.0.1'
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
      EMPRESA_COD,
      ESTADO,
      USR_CREACION,
      FE_CREACION,
      IP_CREACION
    )
    VALUES
    (
      DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
      Ln_IdParametroTiempoLiberar,
      'MD',
      'R1',
      'FO',
      '15',
      'gis_gye@telconet.ec,aventas_gye@telconet.ec,jefaturas_prov_r1@telconet.ec,ventas_provincias@telconet.ec,',
      '18',
      'Activo',
      'mlcruz',
      CURRENT_TIMESTAMP,
      '127.0.0.1'
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
      EMPRESA_COD,
      ESTADO,
      USR_CREACION,
      FE_CREACION,
      IP_CREACION
    )
    VALUES
    (
      DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
      Ln_IdParametroTiempoLiberar,
      'MD',
      'R2',
      'FO',
      '15',
      'gis_uio@telconet.ec,aventas_uio@telconet.ec,jefaturas_prov_r2@telconet.ec,ventas_provincias@telconet.ec,',
      '18',
      'Activo',
      'mlcruz',
      CURRENT_TIMESTAMP,
      '127.0.0.1'
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
      EMPRESA_COD,
      ESTADO,
      USR_CREACION,
      FE_CREACION,
      IP_CREACION
    )
    VALUES
    (
      DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
      Ln_IdParametroTiempoLiberar,
      'MD',
      'R1',
      'RAD',
      '15',
      'radioenlace_gye@telconet.ec,aventas_gye@telconet.ec,jefaturas_prov_r1@telconet.ec,ventas_provincias@telconet.ec,',
      '18',
      'Activo',
      'mlcruz',
      CURRENT_TIMESTAMP,
      '127.0.0.1'
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
      EMPRESA_COD,
      ESTADO,
      USR_CREACION,
      FE_CREACION,
      IP_CREACION
    )
    VALUES
    (
      DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
      Ln_IdParametroTiempoLiberar,
      'MD',
      'R2',
      'RAD',
      '15',
      'radioenlace_uio@telconet.ec,aventas_uio@telconet.ec,jefaturas_prov_r2@telconet.ec,ventas_provincias@telconet.ec,',
      '18',
      'Activo',
      'mlcruz',
      CURRENT_TIMESTAMP,
      '127.0.0.1'
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
      EMPRESA_COD,
      ESTADO,
      USR_CREACION,
      FE_CREACION,
      IP_CREACION
    )
    VALUES
    (
      DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
      Ln_IdParametroTiempoLiberar,
      'MD',
      'R1',
      'CO',
      '15',
      'radioenlace_gye@telconet.ec,aventas_gye@telconet.ec,jefaturas_prov_r1@telconet.ec,ventas_provincias@telconet.ec,',
      '18',
      'Activo',
      'mlcruz',
      CURRENT_TIMESTAMP,
      '127.0.0.1'
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
      EMPRESA_COD,
      ESTADO,
      USR_CREACION,
      FE_CREACION,
      IP_CREACION
    )
    VALUES
    (
      DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
      Ln_IdParametroTiempoLiberar,
      'MD',
      'R2',
      'CO',
      '15',
      'radioenlace_uio@telconet.ec,aventas_uio@telconet.ec,jefaturas_prov_r2@telconet.ec,ventas_provincias@telconet.ec,',
      '18',
      'Activo',
      'mlcruz',
      CURRENT_TIMESTAMP,
      '127.0.0.1'
    );
  SYS.DBMS_OUTPUT.PUT_LINE('Registros de parametrización de tiempos y alias insertados Correctamente');
EXCEPTION
WHEN OTHERS THEN
  SYS.DBMS_OUTPUT.PUT_LINE('Error: '|| SQLCODE || ' - ERROR_STACK: ' || DBMS_UTILITY.FORMAT_ERROR_STACK || ' - ERROR_BACKTRACE: ' || DBMS_UTILITY.FORMAT_ERROR_BACKTRACE);
  ROLLBACK;
END;
/
COMMIT;

SET DEFINE OFF 
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
    'NOTIFICACION LIBERACIÓN DE FACTIBILIDAD',
    'LIBERA_FACTIB',
    'TECNICO',
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
                                El presente correo es para informarle que se procedi&oacute; a la anulaci&oacute;n de los servicios por liberaci&oacute;n de factibilidad. Se adjunta el archivo con los servicios anulados.
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td>
                    &nbsp;
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
    'NOTIFICACION ANULACIÓN DE SERVICIO POR LIBERACIÓN DE FACTIBILIDAD',
    'ANULASERVFACTIB',
    'TECNICO',
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
                                <strong>Producto/Plan:</strong>
                            </td>
                            <td>{{ PRODUCTO_PLAN }}</td>
                        </tr>
                        <tr>
                            <td>
                                <strong>Fecha de Factibilidad:</strong>
                            </td>
                            <td>{{ FECHA_FACTIBILIDAD }}</td>
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
                <td>
                    &nbsp;
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
COMMIT;
