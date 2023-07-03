SET DEFINE OFF 
--Creación de plantilla y alias que se enviará al realizar el proceso de migración de licencias de Internet Protegido de McAfee a Kasperky
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
      'NOTIFICACIÓN AUTOMÁTICA AL REALIZAR MIGRACIÓN MASIVA DE SERVICIOS INTERNET PROTEGIDO',
      'MIGRAIPROTEGIDO',
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
                                  El presente correo es para informarle que se procedi&oacute; con la ejecuci&oacute;n del proceso de 
                                  migraci&oacute;n masiva de licencias de Internet Protegido de McAfee a Kaspersky. <br>Se adjunta el archivo 
                                  con el detalle de la migraci&oacute;n.
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
  WHERE CODIGO='MIGRAIPROTEGIDO';
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
  SYS.DBMS_OUTPUT.PUT_LINE('Se creó la plantilla correctamente MIGRAMASIMCAFEE');
  COMMIT;
EXCEPTION
WHEN OTHERS THEN
  SYS.DBMS_OUTPUT.PUT_LINE('Error: '|| SQLCODE || ' - ERROR_STACK: ' || DBMS_UTILITY.FORMAT_ERROR_STACK || ' - ERROR_BACKTRACE: ' 
                            || DBMS_UTILITY.FORMAT_ERROR_BACKTRACE);
  ROLLBACK;
END;
/

--Creación de plantilla y alias que se enviará al realizar el proceso de migración de licencias de Internet Protegido de McAfee a Kasperky
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
      'NOTIFICACIÓN AUTOMÁTICA CON EL CONSOLIDADO DE SERVICIOS MCAFEE A CANCELAR',
      'MIGRAMASIMCAFEE',
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
                                  El presente correo es para informarle que se ejecut&oacute; un nuevo proceso de migración masiva 
                                  de licencias de Internet Protegido de McAfee a Kaspersky. <br>Se adjunta el archivo con el detalle de los
                                  servicios cancelados de manera l&oacute;gica.
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
  WHERE CODIGO='MIGRAMASIMCAFEE';
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
  SYS.DBMS_OUTPUT.PUT_LINE('Se creó la plantilla correctamente MIGRAMASIMCAFEE');
  COMMIT;
EXCEPTION
WHEN OTHERS THEN
  SYS.DBMS_OUTPUT.PUT_LINE('Error: '|| SQLCODE || ' - ERROR_STACK: ' || DBMS_UTILITY.FORMAT_ERROR_STACK || ' - ERROR_BACKTRACE: ' 
                            || DBMS_UTILITY.FORMAT_ERROR_BACKTRACE);
  ROLLBACK;
END;
/
--Asociación de nueva característica para licencias McAfee que dieron error al realizar la migración masiva
DECLARE
  Ln_IdProductoUno              NUMBER(5,0) := 209;
  Ln_IdProductoDos              NUMBER(5,0) := 210;
  Ln_IdProductoTres             NUMBER(5,0) := 211;
  Ln_IdProductoCuatro           NUMBER(5,0) := 212;
  Ln_IdCaractErrorMigraMcAfee   NUMBER(5,0);
  Lv_EstadoActivo               VARCHAR2(6) := 'Activo';
BEGIN
  Ln_IdCaractErrorMigraMcAfee := DB_COMERCIAL.SEQ_ADMI_CARACTERISTICA.NEXTVAL;
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
      Ln_IdCaractErrorMigraMcAfee,
      'ERROR_REPORTE_MIGRACION_MASIVA_MCAFEE',
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
      Ln_IdProductoUno,
      Ln_IdCaractErrorMigraMcAfee,
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
      Ln_IdProductoDos,
      Ln_IdCaractErrorMigraMcAfee,
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
      Ln_IdProductoTres,
      Ln_IdCaractErrorMigraMcAfee,
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
      Ln_IdProductoCuatro,
      Ln_IdCaractErrorMigraMcAfee,
      CURRENT_TIMESTAMP,
      'mlcruz',
      Lv_EstadoActivo,
      'NO'
    );
  SYS.DBMS_OUTPUT.PUT_LINE('Creación correctamente de la asociación de producto y la característica para el reporte de migración');
  COMMIT;
EXCEPTION
WHEN OTHERS THEN
  SYS.DBMS_OUTPUT.PUT_LINE('Error: '|| SQLCODE || ' - ERROR_STACK: ' || DBMS_UTILITY.FORMAT_ERROR_STACK 
                           || ' - ERROR_BACKTRACE: ' || DBMS_UTILITY.FORMAT_ERROR_BACKTRACE);
  ROLLBACK;
END;
/
--Asociación de nueva característica para licencias McAfee que dieron error al realizar la migración masiva
DECLARE
  Ln_IdProductoUno              NUMBER(5,0) := 209;
  Ln_IdProductoDos              NUMBER(5,0) := 210;
  Ln_IdProductoTres             NUMBER(5,0) := 211;
  Ln_IdProductoCuatro           NUMBER(5,0) := 212;
  Ln_IdCaractErrorMigraMcAfee   NUMBER(5,0);
  Lv_EstadoActivo               VARCHAR2(6) := 'Activo';
BEGIN
  Ln_IdCaractErrorMigraMcAfee := DB_COMERCIAL.SEQ_ADMI_CARACTERISTICA.NEXTVAL;
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
      Ln_IdCaractErrorMigraMcAfee,
      'ERROR_INFO_MIGRACION_MASIVA_MCAFEE',
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
      Ln_IdProductoUno,
      Ln_IdCaractErrorMigraMcAfee,
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
      Ln_IdProductoDos,
      Ln_IdCaractErrorMigraMcAfee,
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
      Ln_IdProductoTres,
      Ln_IdCaractErrorMigraMcAfee,
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
      Ln_IdProductoCuatro,
      Ln_IdCaractErrorMigraMcAfee,
      CURRENT_TIMESTAMP,
      'mlcruz',
      Lv_EstadoActivo,
      'NO'
    );
  SYS.DBMS_OUTPUT.PUT_LINE('Creación correctamente de la asociación de producto y la característica con la información de la migración');
  COMMIT;
EXCEPTION
WHEN OTHERS THEN
  SYS.DBMS_OUTPUT.PUT_LINE('Error: '|| SQLCODE || ' - ERROR_STACK: ' || DBMS_UTILITY.FORMAT_ERROR_STACK 
                           || ' - ERROR_BACKTRACE: ' || DBMS_UTILITY.FORMAT_ERROR_BACKTRACE);
  ROLLBACK;
END;
/
UPDATE DB_GENERAL.ADMI_PARAMETRO_DET 
SET VALOR7 = 'http://34.211.179.77/GMSXSP/rest/kss_service' 
WHERE PARAMETRO_ID = 
(SELECT ID_PARAMETRO
FROM DB_GENERAL.ADMI_PARAMETRO_CAB CAB
WHERE CAB.NOMBRE_PARAMETRO = 'ANTIVIRUS_PLANES_Y_PRODS_MD'
AND CAB.ESTADO = 'Activo')
AND VALOR1 = 'NUEVO'
AND ESTADO = 'Activo';

UPDATE DB_GENERAL.ADMI_PARAMETRO_DET 
SET VALOR2 = '2' 
WHERE PARAMETRO_ID = 
(SELECT ID_PARAMETRO
FROM DB_GENERAL.ADMI_PARAMETRO_CAB CAB
WHERE CAB.NOMBRE_PARAMETRO = 'ANTIVIRUS_KASPERSKY_LICENCIAS_MD'
AND CAB.ESTADO = 'Activo')
AND VALOR1 = '1'
AND ESTADO = 'Activo';

UPDATE DB_GENERAL.ADMI_PARAMETRO_DET 
SET VALOR2 = '2.75' 
WHERE PARAMETRO_ID = 
(SELECT ID_PARAMETRO
FROM DB_GENERAL.ADMI_PARAMETRO_CAB CAB
WHERE CAB.NOMBRE_PARAMETRO = 'ANTIVIRUS_KASPERSKY_LICENCIAS_MD'
AND CAB.ESTADO = 'Activo')
AND VALOR1 = '3'
AND ESTADO = 'Activo';
COMMIT;
/