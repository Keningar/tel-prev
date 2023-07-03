SET SERVEROUTPUT ON
--Se ingresa el nuevo token usado para los procesos masivos por migración de tecnología del producto I. PROTEGIDO MULTI PAID
DECLARE
  Ln_Aplicacion_Token NUMBER;
BEGIN
  --Crear Aplicacion 
  INSERT
  INTO DB_TOKENSECURITY.APPLICATION VALUES
    (
      DB_TOKENSECURITY.SEQ_APPLICATION.NEXTVAL,
      'PROCESOS_MASIVOS_MD',
      'ACTIVO',
      30
    );

  -- Obtener id de la aplicacion 
  SELECT id_application
  INTO Ln_Aplicacion_Token
  FROM DB_TOKENSECURITY.APPLICATION
  WHERE name = 'PROCESOS_MASIVOS_MD';

  --Configurar clase InternetProtegidoWSController y relacionarlo con el PROCESOS_MASIVOS_MD
  INSERT
  INTO DB_TOKENSECURITY.WEB_SERVICE VALUES
    (
      DB_TOKENSECURITY.SEQ_WEB_SERVICE.nextval,
      'InternetProtegidoWSController',
      'procesarAction',
      1,
      'ACTIVO',
      Ln_Aplicacion_Token
    );

  --Configurar Usuario/Clave PROCESOS_MASIVOS_MD/PROCESOSMASIVOSMIGRACIONTECNOLOGIAIPMP(sha256)
  INSERT
  INTO DB_TOKENSECURITY.USER_TOKEN VALUES
    (
      DB_TOKENSECURITY.SEQ_USER_TOKEN.nextval,
      'PROCESOS_MASIVOS_MD',
      '141CF19D11C3F678AFC0A82CBE9CB88DA49ADE53E6A80982800B68F841D702AB',
      'Activo',
      Ln_Aplicacion_Token
    );
  COMMIT;
  SYS.DBMS_OUTPUT.PUT_LINE('Registros insertados Correctamente');
EXCEPTION
WHEN OTHERS THEN
  SYS.DBMS_OUTPUT.PUT_LINE('Error: '||SQLERRM);
  ROLLBACK;
END;
/
SET DEFINE OFF;
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
      'Notificación enviada al no reactivarse el producto I. PROTEGIDO MULTI PAID con tecnología Kaspersky',
      'ERRORREACTIPMP',
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
                                El presente correo es para informarle que no se ha podido reactivar el {{ nombreProducto }} {{ descripcionServicio }}
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
  WHERE CODIGO='ERRORREACTIPMP';
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
  SYS.DBMS_OUTPUT.PUT_LINE('Se creó la plantilla correctamente ERRORREACTIPMP con sus respectivos alias');
  COMMIT;
EXCEPTION
WHEN OTHERS THEN
  SYS.DBMS_OUTPUT.PUT_LINE('Error: '|| SQLCODE || ' - ERROR_STACK: ' || DBMS_UTILITY.FORMAT_ERROR_STACK || ' - ERROR_BACKTRACE: ' 
                           || DBMS_UTILITY.FORMAT_ERROR_BACKTRACE);
  ROLLBACK;
END;
/
--Plantilla usada para notificar que no se ha podido cancelar las licencias I. PROTEGIDO MULTI PAID con tecnología Kaspersky
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
      'Notificación enviada al no cancelarse el producto I. PROTEGIDO MULTI PAID con tecnología Kaspersky',
      'ERRORCANMASIPMP',
      'TECNICO',
      'Activo',
      CURRENT_TIMESTAMP,
      'mlcruz',
      TO_CLOB('<html>
   <head>
      <meta http-equiv=Content-Type content="text/html; charset=UTF-8">
      <style type="text/css">
table.cssTable {font-family:verdana,arial,sans-serif;font-size:11px;color:#333333;border-width: 1px;border-color: #999999;border-collapse:collapse;}
table.cssTable th {background-color:#c3dde0;border-width: 1px;padding: 8px;border-style: solid;border-color: #a9c6c9;}
table.cssTable tr {background-color:#d4e3e5;}table.cssTable td {border-width: 1px;padding: 8px;border-style: solid;border-color: #a9c6c9;}
table.cssTblPrincipal{font-family: verdana,arial,sans-serif;font-size:12px;}
</style>
   </head>
   <body>
      <table class = "cssTblPrincipal" align="center" width="100%" cellspacing="0" cellpadding="5">
         <tr>
            <td align="center" style="border:1px solid #6699CC;background-color:#E5F2FF;"><img alt="" 
src="http://images.telconet.net/others/sit/notificaciones/logo.png"/></td>
         </tr>
         <tr>
            <td style="border:1px solid #6699CC;">
               <table width="100%" cellspacing="0" cellpadding="5">
                  <tr>
                     <td colspan="2">Estimado personal,</td>
                  </tr>
                  <tr>
                     <td colspan="2">El presente correo es para indicarle que las siguientes suscripciones presentaron problemas 
al intentar ser canceladas con Kaspersky :</td>
                  </tr>
                  
                  <tr>
                     <td colspan="2">
                        <table class = "cssTable"  align="center" >
                           <tr>
                              <th> # </th>
                              <th> Login </th>
                              <th> Suscripci&oacute;n <br></th>
                           </tr>
                           {{ registrosSuscripciones }}
                        </table>
                     </td>
                  </tr>
                  <tr>
                     <td colspan="2">
                        <hr />
                     </td>
                  </tr>
               </table>
            </td>
         </tr>
         <tr>
            <td></td>
         </tr>
      </table>
   </body>
</html>
')
  );
  SELECT ID_PLANTILLA
  INTO Ln_IdPlantilla
  FROM DB_COMUNICACION.ADMI_PLANTILLA
  WHERE CODIGO='ERRORCANMASIPMP';
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
  SYS.DBMS_OUTPUT.PUT_LINE('Se creó la plantilla correctamente ERRORCANMASIPMP con sus respectivos alias');
  COMMIT;
EXCEPTION
WHEN OTHERS THEN
  SYS.DBMS_OUTPUT.PUT_LINE('Error: '|| SQLCODE || ' - ERROR_STACK: ' || DBMS_UTILITY.FORMAT_ERROR_STACK || ' - ERROR_BACKTRACE: ' 
                           || DBMS_UTILITY.FORMAT_ERROR_BACKTRACE);
  ROLLBACK;
END;
/
/*
 * Asociación de características ERROR_CORTE_INTERNET_PROTEGIDO, ERROR_REACTIVACION_INTERNET_PROTEGIDO y ERROR_CANCELACION_INTERNET_PROTEGIDO
 * para producto I. PROTEGIDO MULTI PAID
 */
DECLARE
  Ln_IdCaractErrorCorteIProteg      NUMBER(5,0);
  Ln_IdCaractErrorReactIProteg      NUMBER(5,0);
  Ln_IdCaractErrorCancelIProteg     NUMBER(5,0);
  Lv_EstadoActivo                   VARCHAR2(6) := 'Activo';
BEGIN
  Ln_IdCaractErrorCorteIProteg := DB_COMERCIAL.SEQ_ADMI_CARACTERISTICA.NEXTVAL;
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
      Ln_IdCaractErrorCorteIProteg,
      'ERROR_CORTE_INTERNET_PROTEGIDO',
      'T',
      Lv_EstadoActivo,
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
      210,
      Ln_IdCaractErrorCorteIProteg,
      CURRENT_TIMESTAMP,
      'mlcruz',
      Lv_EstadoActivo,
      'NO'
    );

  Ln_IdCaractErrorReactIProteg := DB_COMERCIAL.SEQ_ADMI_CARACTERISTICA.NEXTVAL;
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
      Ln_IdCaractErrorReactIProteg,
      'ERROR_REACTIVACION_INTERNET_PROTEGIDO',
      'T',
      Lv_EstadoActivo,
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
      210,
      Ln_IdCaractErrorReactIProteg,
      CURRENT_TIMESTAMP,
      'mlcruz',
      Lv_EstadoActivo,
      'NO'
    );

  Ln_IdCaractErrorCancelIProteg := DB_COMERCIAL.SEQ_ADMI_CARACTERISTICA.NEXTVAL;
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
      Ln_IdCaractErrorCancelIProteg,
      'ERROR_CANCELACION_INTERNET_PROTEGIDO',
      'T',
      Lv_EstadoActivo,
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
      210,
      Ln_IdCaractErrorCancelIProteg,
      CURRENT_TIMESTAMP,
      'mlcruz',
      Lv_EstadoActivo,
      'NO'
    );
  SYS.DBMS_OUTPUT.PUT_LINE('Creación correctamente de la asociación del producto '
                           || 'I. PROTEGIDO MULTI PAID y laS características ERROR_CORTE_INTERNET_PROTEGIDO, ERROR_REACTIVACION_INTERNET_PROTEGIDO'
                           || ' y ERROR_CANCELACION_INTERNET_PROTEGIDO');
  COMMIT;
EXCEPTION
WHEN OTHERS THEN
  SYS.DBMS_OUTPUT.PUT_LINE('Error: '|| SQLCODE || ' - ERROR_STACK: ' || DBMS_UTILITY.FORMAT_ERROR_STACK 
                           || ' - ERROR_BACKTRACE: ' || DBMS_UTILITY.FORMAT_ERROR_BACKTRACE);
  ROLLBACK;
END;
/
--Se agrega la parametrización con los nombres de los antivirus usados dentro de los planes y como productos adicionales
DECLARE
  Ln_IdParamAntivirus NUMBER(5,0);
BEGIN
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
    'MASIVO',
    'KASPERSKY',
    'I. PROTEGIDO MULTI PAID',
    'KISMD',
    'b6H7ZoJNcKFZ0c3JJuPERLmDkgryxrB8rLz8jIaNR2o=',
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
  SYS.DBMS_OUTPUT.PUT_LINE('Se creó correctamente el detalle del parámetro ANTIVIRUS_PLANES_Y_PRODS_MD para los procesos masivos');
  COMMIT;
EXCEPTION
WHEN OTHERS THEN
  SYS.DBMS_OUTPUT.PUT_LINE('Error: '|| SQLCODE || ' - ERROR_STACK: ' || DBMS_UTILITY.FORMAT_ERROR_STACK 
                           || ' - ERROR_BACKTRACE: ' || DBMS_UTILITY.FORMAT_ERROR_BACKTRACE);
  ROLLBACK;
END;
/
UPDATE DB_GENERAL.ADMI_PARAMETRO_DET
SET VALOR2 = 'INDIVIDUAL'
WHERE PARAMETRO_ID = (
  SELECT ID_PARAMETRO
  FROM DB_GENERAL.ADMI_PARAMETRO_CAB
  WHERE NOMBRE_PARAMETRO='LOGINES_PILOTO_KASPERSKY'
  AND ESTADO = 'Activo')
AND ESTADO = 'Activo';

--Se eliminan los procesos que quedaron pendientes al realizar el cambio de plan masivo y se actualiza a FIN los detalles de los OLTS
UPDATE DB_COMERCIAL.INFO_DETALLE_SOLICITUD
SET ESTADO                  = 'EliminadaCPM',
OBSERVACION = 'Eliminada por solicitud de IPCC sin ejecución de cambio de plan' 
WHERE ID_DETALLE_SOLICITUD IN
  (SELECT SOL.ID_DETALLE_SOLICITUD
  FROM DB_COMERCIAL.INFO_DETALLE_SOLICITUD SOL
  INNER JOIN DB_COMERCIAL.ADMI_TIPO_SOLICITUD TIPO_SOL
  ON TIPO_SOL.ID_TIPO_SOLICITUD        = SOL.TIPO_SOLICITUD_ID
  WHERE TIPO_SOL.DESCRIPCION_SOLICITUD = 'SOLICITUD CAMBIO PLAN MASIVO'
  AND SOL.ESTADO                       = 'Pendiente'
  AND SOL.SERVICIO_ID IS NOT NULL
  AND SOL.ELEMENTO_ID IS NOT NULL
  );

UPDATE DB_COMERCIAL.INFO_DETALLE_SOLICITUD
SET ESTADO                  = 'EliminadaCPM',
OBSERVACION = 'Eliminada por solicitud de IPCC sin ejecución de cambio de plan' 
WHERE ID_DETALLE_SOLICITUD IN
  (SELECT SOL.ID_DETALLE_SOLICITUD
  FROM DB_COMERCIAL.INFO_DETALLE_SOLICITUD SOL
  INNER JOIN DB_COMERCIAL.ADMI_TIPO_SOLICITUD TIPO_SOL
  ON TIPO_SOL.ID_TIPO_SOLICITUD        = SOL.TIPO_SOLICITUD_ID
  WHERE TIPO_SOL.DESCRIPCION_SOLICITUD = 'SOLICITUD MIGRACION NUEVOS PLANES'
  AND SOL.ESTADO                       = 'Pendiente'
  );

UPDATE DB_INFRAESTRUCTURA.INFO_PROCESO_MASIVO_DET
SET ESTADO = 'EliminadaCPM',
OBSERVACION = 'Eliminada por solicitud de IPCC sin ejecución de cambio de plan' 
WHERE PROCESO_MASIVO_CAB_ID IN 
(
select IPMC.ID_PROCESO_MASIVO_CAB
FROM DB_INFRAESTRUCTURA.INFO_PROCESO_MASIVO_CAB IPMC
WHERE IPMC.TIPO_PROCESO = 'CambioPlanMasivo'
AND IPMC.ESTADO = 'Pendiente'
AND IPMC.EMPRESA_ID = 18
)
AND ESTADO = 'Pendiente';

UPDATE DB_INFRAESTRUCTURA.INFO_PROCESO_MASIVO_DET
SET ESTADO = 'EliminadaCPM',
OBSERVACION = 'Eliminada por solicitud de IPCC sin ejecución de cambio de plan' 
WHERE PROCESO_MASIVO_CAB_ID IN 
(
select IPMC.ID_PROCESO_MASIVO_CAB
FROM DB_INFRAESTRUCTURA.INFO_PROCESO_MASIVO_CAB IPMC
WHERE IPMC.TIPO_PROCESO = 'CambioPlanMasivo'
AND IPMC.ESTADO = 'Pendiente'
AND IPMC.EMPRESA_ID = 18
)
AND ESTADO = 'PrePendiente';

UPDATE DB_INFRAESTRUCTURA.INFO_PROCESO_MASIVO_CAB
SET ESTADO = 'EliminadaCPM' 
WHERE TIPO_PROCESO = 'CambioPlanMasivo'
AND ESTADO = 'Pendiente'
AND EMPRESA_ID = 18;

UPDATE DB_INFRAESTRUCTURA.INFO_DETALLE_ELEMENTO
SET DETALLE_VALOR          ='FIN'
WHERE ID_DETALLE_ELEMENTO IN
  (SELECT IDE_MIDDLEWARE.ID_DETALLE_ELEMENTO
  FROM DB_INFRAESTRUCTURA.info_elemento OLT,
    DB_INFRAESTRUCTURA.INFO_EMPRESA_ELEMENTO_UBICA ELEUBI,
    DB_INFRAESTRUCTURA.INFO_DETALLE_ELEMENTO IDE_MIDDLEWARE,
    DB_INFRAESTRUCTURA.admi_modelo_elemento modelo,
    DB_INFRAESTRUCTURA.admi_tipo_elemento TIPO
  WHERE OLT.ID_ELEMENTO             = ELEUBI.ELEMENTO_ID
  AND OLT.MODELO_ELEMENTO_ID        = modelo.ID_MODELO_ELEMENTO
  AND modelo.TIPO_ELEMENTO_ID       = TIPO.ID_TIPO_ELEMENTO
  AND TIPO.NOMBRE_TIPO_ELEMENTO     = 'OLT'
  AND ELEUBI.EMPRESA_COD            = '18'
  AND OLT.ID_ELEMENTO               = IDE_MIDDLEWARE.ELEMENTO_ID
  AND IDE_MIDDLEWARE.DETALLE_NOMBRE = 'CAMBIO_PLAN_MASIVO_MD'
  AND IDE_MIDDLEWARE.DETALLE_VALOR != 'FIN'
  AND IDE_MIDDLEWARE.ESTADO         = 'Activo'
  AND (OLT.ESTADO                   = 'Activo'
  OR OLT.ESTADO                     = 'Modificado')
  );
COMMIT;
/