/**
 * Se crea dml para plantilla de reporte
 * 
 * @author Hector Lozano <hlozano@telconet.ec>
 * @since 1.0 25-01-2021
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
    'Reporte de NC,pagos,anticipos que estan enlazados a facturas sin gestion',    
    'RPT_DOC_RECHAZA',
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
<tr><td style="border:1px solid #6699CC;">
    <table width="100%" cellspacing="0" cellpadding="5">
    <tr><td colspan="2">Estimados,</td></tr>
    <tr>
    <td colspan="2">
    En el presente mail se adjunta Reporte de NC, pagos, anticipos que estan enlazados a facturas sin gestion. Alguna novedad favor notificar a Sistemas.
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
      WHERE CODIGO = 'RPT_DOC_RECHAZA'
      AND ESTADO   = 'Activo'
    ),
    'Activo',
    SYSDATE,
    'hlozano',
    'NO'
  );

COMMIT;
/




