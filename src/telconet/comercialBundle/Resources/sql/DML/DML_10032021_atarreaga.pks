/**
 * Se crea dml para plantilla de reporte
 * 
 * @author Alex Arreaga <atarreaga@telconet.ec>
 * @since 1.0 10-03-2021
 */

INSERT INTO
  DB_COMUNICACION.ADMI_PLANTILLA
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
    'Reporte Solicitudes recálculo',    
    'RPT_RECALCULO',
    'COMERCIAL',
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
<tr><td style="border:1px solid #6699CC;">
    <table width="100%" cellspacing="0" cellpadding="5">
    <tr><td colspan="2">Estimados,</td></tr>
    <tr>
    <td colspan="2">
    En el presente mail se adjunta Reporte solicitudes recalculadas. Alguna novedad favor notificar a Sistemas.
    </td></tr>
    <tr><td colspan="2"><hr /></td></tr>
    </table>
</td></tr>
<tr><td></td></tr></table></body></html>
',
    'Activo',
    SYSDATE,
    'atarreaga'
  );

INSERT INTO
  DB_COMUNICACION.INFO_ALIAS_PLANTILLA
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
    (
      SELECT ID_ALIAS
      FROM DB_COMUNICACION.ADMI_ALIAS
      WHERE VALOR      = 'facturacion@netlife.net.ec'
      AND ESTADO       IN ('Activo','Modificado')
      AND EMPRESA_COD  = '18'    
    ),
    (
      SELECT ID_PLANTILLA
      FROM DB_COMUNICACION.ADMI_PLANTILLA
      WHERE CODIGO = 'RPT_RECALCULO'
      AND ESTADO   = 'Activo'
    ),
    'Activo',
    SYSDATE,
    'atarreaga',
    'NO'
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
    ESTADO,
    USR_CREACION,
    FE_CREACION,
    IP_CREACION,
    USR_ULT_MOD,
    FE_ULT_MOD,
    IP_ULT_MOD,
    VALOR5,
    EMPRESA_COD,
    OBSERVACION
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (
      SELECT ID_PARAMETRO
      FROM DB_GENERAL.ADMI_PARAMETRO_CAB
      WHERE NOMBRE_PARAMETRO = 'PARAM_FLUJO_ADULTO_MAYOR'
      AND ESTADO             = 'Activo'
    ),
    'MENSAJE_RECALCULO_HIST',
    'Se ejecutó de manera automática el proceso de recálculo. Descuento anterior:$ #DESCANTERIOR , Descuento actual:$ #DESCACTUAL',
    NULL,
    NULL,
    NULL,
    'Activo',
    'atarreaga',
    SYSDATE,
    '172.17.0.1',
    'atarreaga',
    SYSDATE,
    '172.17.0.1',
    NULL,
    '18',
    'Contiene el mensaje para guardar el historial por recálculo de las solicitudes.'
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
    ESTADO,
    USR_CREACION,
    FE_CREACION,
    IP_CREACION,
    USR_ULT_MOD,
    FE_ULT_MOD,
    IP_ULT_MOD,
    VALOR5,
    EMPRESA_COD,
    OBSERVACION
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (
      SELECT ID_PARAMETRO
      FROM DB_GENERAL.ADMI_PARAMETRO_CAB
      WHERE NOMBRE_PARAMETRO = 'PARAM_FLUJO_ADULTO_MAYOR'
      AND ESTADO             = 'Activo'
    ),
    'TIPO_PROCESO',
    'INDIVIDUAL',
    NULL,
    NULL,
    NULL,
    'Activo',
    'atarreaga',
    SYSDATE,
    '172.17.0.1',
    'atarreaga',
    SYSDATE,
    '172.17.0.1',
    NULL,
    '18',
    'Contiene el tipo de proceso a ejecutar.'
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
    ESTADO,
    USR_CREACION,
    FE_CREACION,
    IP_CREACION,
    USR_ULT_MOD,
    FE_ULT_MOD,
    IP_ULT_MOD,
    VALOR5,
    EMPRESA_COD,
    OBSERVACION
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (
      SELECT ID_PARAMETRO
      FROM DB_GENERAL.ADMI_PARAMETRO_CAB
      WHERE NOMBRE_PARAMETRO = 'PARAM_FLUJO_ADULTO_MAYOR'
      AND ESTADO             = 'Activo'
    ),
    'TIPO_PROCESO',
    'MASIVO',
    NULL,
    NULL,
    NULL,
    'Activo',
    'atarreaga',
    SYSDATE,
    '172.17.0.1',
    'atarreaga',
    SYSDATE,
    '172.17.0.1',
    NULL,
    '18',
    'Contiene el tipo de proceso a ejecutar.'
  );

--caracteristicas
INSERT INTO DB_COMERCIAL.ADMI_CARACTERISTICA (
ID_CARACTERISTICA,
DESCRIPCION_CARACTERISTICA,
TIPO_INGRESO,
ESTADO,
FE_CREACION,
USR_CREACION,
TIPO,
DETALLE_CARACTERISTICA
) VALUES (
DB_COMERCIAL.SEQ_ADMI_CARACTERISTICA.NEXTVAL,
'ID_DET_CARACT_ADULTO_MAYOR',
'N',
'Activo',
SYSDATE,
'atarreaga',
'COMERCIAL',
NULL);

INSERT INTO DB_COMERCIAL.ADMI_CARACTERISTICA (
ID_CARACTERISTICA,
DESCRIPCION_CARACTERISTICA,
TIPO_INGRESO,
ESTADO,
FE_CREACION,
USR_CREACION,
TIPO,
DETALLE_CARACTERISTICA
) VALUES (
DB_COMERCIAL.SEQ_ADMI_CARACTERISTICA.NEXTVAL,
'VALOR_DESCUENTO',
'N',
'Activo',
SYSDATE,
'atarreaga',
'COMERCIAL',
NULL);


COMMIT;
/
