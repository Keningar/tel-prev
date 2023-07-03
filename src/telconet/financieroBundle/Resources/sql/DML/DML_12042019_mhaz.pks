/**
 * @author Madeline Haz <mhaz@telconet.ec>
 * @version 1.0
 * @since 12-04-2019    
* Se crean las sentencias DML para insertar parámetros (CAB y DET) relacionados con estados de las notas de crédito.
 */
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_CAB(
  ID_PARAMETRO,
  NOMBRE_PARAMETRO,
  DESCRIPCION,
  MODULO,
  PROCESO,
  ESTADO,
  USR_CREACION,
  FE_CREACION,
  IP_CREACION,
  USR_ULT_MOD,
  FE_ULT_MOD,
  IP_ULT_MOD
) VALUES(
    DB_GENERAL.SEQ_ADMI_PARAMETRO_CAB.NEXTVAL,
    'ESTADOS_NOTA_CREDITO',
    'PARAMETRO QUE DEFINE LOS ESTADOS DE LA NOTA DE CREDITO',
    'FINANCIERO',
    'ESTADOS_NC',
    'Activo',
    'mhaz',
    SYSDATE,
    '172.17.0.1',
    'mhaz',        
    NULL,
    NULL
);    
COMMIT;     
-- DETALLE
-- Estado Activo
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET(
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
  EMPRESA_COD,
  VALOR6
) VALUES (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (SELECT ID_PARAMETRO
     FROM DB_GENERAL.ADMI_PARAMETRO_CAB
     WHERE NOMBRE_PARAMETRO = 'ESTADOS_NOTA_CREDITO'
     AND MODULO  = 'FINANCIERO'
     AND PROCESO = 'ESTADOS_NC'
     AND ESTADO  = 'Activo'),
    'PARAMETRO QUE VALIDA EL ESTADO DE LA NOTA DE CREDITO',
    'VALIDA_ESTADOS_NC',
    'Activo',
    NULL,
    NULL,
    'Activo',
    'mhaz',
    SYSDATE,
    '127.0.0.1',
    NULL,
    NULL,
    NULL,
    NULL,
    '18',
    NULL
);
-- Estado Aprobada
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET(
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
  EMPRESA_COD,
  VALOR6
) VALUES (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (SELECT ID_PARAMETRO
     FROM DB_GENERAL.ADMI_PARAMETRO_CAB
     WHERE NOMBRE_PARAMETRO = 'ESTADOS_NOTA_CREDITO'
     AND MODULO  = 'FINANCIERO'
     AND PROCESO = 'ESTADOS_NC'
     AND ESTADO  = 'Activo'),
    'PARAMETRO QUE VALIDA EL ESTADO DE LA NOTA DE CREDITO',
    'VALIDA_ESTADOS_NC',
    'Aprobada',
    NULL,
    NULL,
    'Activo',
    'mhaz',
    SYSDATE,
    '127.0.0.1',
    NULL,
    NULL,
    NULL,
    NULL,
    '18',
    NULL
);
-- Estado Pendiente
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET(
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
  EMPRESA_COD,
  VALOR6
) VALUES (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (SELECT ID_PARAMETRO
     FROM DB_GENERAL.ADMI_PARAMETRO_CAB
     WHERE NOMBRE_PARAMETRO = 'ESTADOS_NOTA_CREDITO'
     AND MODULO  = 'FINANCIERO'
     AND PROCESO = 'ESTADOS_NC'
     AND ESTADO  = 'Activo'),
    'PARAMETRO QUE VALIDA EL ESTADO DE LA NOTA DE CREDITO',
    'VALIDA_ESTADOS_NC',
    'Pendiente',
    NULL,
    NULL,
    'Activo',
    'mhaz',
    SYSDATE,
    '127.0.0.1',
    NULL,
    NULL,
    NULL,
    NULL,
    '18',
    NULL
);
-- Inserta nueva plantilla para notificar la No creación de una NC
INSERT INTO DB_COMUNICACION.ADMI_PLANTILLA(
  ID_PLANTILLA,
  NOMBRE_PLANTILLA,
  CODIGO,
  MODULO,
  PLANTILLA,
  ESTADO,
  FE_CREACION,
  USR_CREACION,
  FE_ULT_MOD,
  USR_ULT_MOD,
  EMPRESA_COD
) VALUES (
    DB_COMUNICACION.SEQ_ADMI_PLANTILLA.NEXTVAL,
    'Notifica la No creación de NC',
    'NOTIFICA_NC',
    'FINANCIERO',
    '<html>
        <head>
            <meta http-equiv=Content-Type content="text/html; charset=UTF-8">
            <style type="text/css">table.cssTable { font-family: verdana,arial,sans-serif;font-size:11px;color:#333333;border-width: 1px;border-color: #999999;border-collapse: collapse;}table.cssTable th {background-color:#c3dde0;border-width: 1px;padding: 8px;border-style: solid;border-color: #a9c6c9;}table.cssTable tr {background-color:#d4e3e5;}table.cssTable td {border-width: 1px;padding: 8px;border-style: solid;border-color: #a9c6c9;}table.cssTblPrincipal{font-family: verdana,arial,sans-serif;font-size:12px;}</style>
        </head>
        <body>
        <table class = "cssTblPrincipal" align="center" width="100%" cellspacing="0" cellpadding="5">
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
            <td colspan="2">El presente correo es para notificar que se intentó crear una Nota de Crédito con Valor Original,
                            pero la factura ya cuenta con una NC:</td>
        </tr>    
        <tr><td colspan="2">
         <table class = "cssTable"  align="center" >    
        <tr><th> Usuario Creacion: </th><td> {{ strUsrCreacion }} </td></tr>    
        <tr><th> Numero Factura: </th><td> {{ strNumFactura }} </td></tr>
        <tr><th> Estado Nota de Credito Existente: </th><td> {{ strEstadoNC }} </td></tr>    
        </table></td></tr><tr><td colspan="2"><hr /></td></tr>
        </table></td></tr><tr><td></td></tr></table>
        </body>
    </html>',
    'Activo',
    SYSDATE,
    'mhaz',
    NULL,
    NULL,
    '18'
    );

-- Insertar Alias Para Notificacion De NC 
INSERT INTO DB_COMUNICACION.INFO_ALIAS_PLANTILLA (
  ID_ALIAS_PLANTILLA,
  ALIAS_ID,
  PLANTILLA_ID,
  ESTADO,
  FE_CREACION,
  USR_CREACION,
  ES_COPIA
) VALUES (
    DB_COMUNICACION.SEQ_INFO_ALIAS_PLANTILLA.NEXTVAL,
    ( SELECT ID_ALIAS
      FROM   DB_COMUNICACION.ADMI_ALIAS
      WHERE  VALOR       = 'cobranzasgye@netlife.net.ec'
      AND    ESTADO IN     ('Activo','Modificado')
      AND    EMPRESA_COD = 18    
    ),
    ( SELECT ID_PLANTILLA
      FROM   DB_COMUNICACION.ADMI_PLANTILLA
      WHERE  CODIGO = 'NOTIFICA_NC'
      AND    ESTADO = 'Activo'
    ),
    'Activo',
    SYSDATE,
    'mhaz',
    'NO'
  );

INSERT INTO DB_COMUNICACION.INFO_ALIAS_PLANTILLA(
  ID_ALIAS_PLANTILLA,
  ALIAS_ID,
  PLANTILLA_ID,
  ESTADO,
  FE_CREACION,
  USR_CREACION,
  ES_COPIA
) VALUES (
    DB_COMUNICACION.SEQ_INFO_ALIAS_PLANTILLA.NEXTVAL,
    ( SELECT ID_ALIAS
      FROM   DB_COMUNICACION.ADMI_ALIAS
      WHERE  VALOR       = 'cobranzasuio@netlife.net.ec'
      AND    ESTADO IN     ('Activo','Modificado')
      AND    EMPRESA_COD = 18    
    ),
    ( SELECT ID_PLANTILLA
      FROM   DB_COMUNICACION.ADMI_PLANTILLA
      WHERE  CODIGO = 'NOTIFICA_NC'
      AND    ESTADO = 'Activo'
    ),
    'Activo',
    SYSDATE,
    'mhaz',
    'NO'
  );
COMMIT;
/
