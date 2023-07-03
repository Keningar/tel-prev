DELETE FROM DB_COMUNICACION.INFO_ALIAS_PLANTILLA 
WHERE
PLANTILLA_ID IN (
    SELECT ID_PLANTILLA
    FROM DB_COMUNICACION.ADMI_PLANTILLA
    WHERE CODIGO = 'RPT_EMER_SANIT'
    AND ESTADO   = 'Activo'
);

--SE ELIMINA PLANTILLA DE CORREO 
DELETE FROM DB_COMUNICACION.ADMI_PLANTILLA 
WHERE CODIGO     = 'RPT_EMER_SANIT' 
AND MODULO       = 'FINANCIERO'
AND ESTADO       = 'Activo';

--PARAMETROS
INSERT 
INTO 
  DB_GENERAL.ADMI_PARAMETRO_CAB 
  (
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
  ) 
  VALUES 
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_CAB.NEXTVAL,
    'PROCESO_EMER_SANITARIA',
    'PARAMETRO PADRE PARA VALORES USADOS EN EL PROYECTO DE EMERGENCIA SANITARIA.',
    'COMERCIAL',
    'EMERGENCIA_SANITARIA',
    'Activo',
    'jcandelario',
    SYSDATE,
    '127.0.0.1',
    'jcandelario',
    SYSDATE,
    '127.0.0.1');
  
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
    VALOR6,
    VALOR7,
    OBSERVACION
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (
      SELECT ID_PARAMETRO
      FROM DB_GENERAL.ADMI_PARAMETRO_CAB
      WHERE NOMBRE_PARAMETRO = 'PROCESO_EMER_SANITARIA'
      AND ESTADO             = 'Activo'
    ),
    'ESTADOS_SERVICIO',
    'Activo',
    NULL,
    NULL,
    NULL,
    'Activo',
    'jcandelario',
    SYSDATE,
    '172.17.0.1',
    'jcandelario',
    SYSDATE,
    '172.17.0.1',
    NULL,
    '18',
    NULL,
    NULL,
    NULL
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
    VALOR6,
    VALOR7,
    OBSERVACION
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (
      SELECT ID_PARAMETRO
      FROM DB_GENERAL.ADMI_PARAMETRO_CAB
      WHERE NOMBRE_PARAMETRO = 'PROCESO_EMER_SANITARIA'
      AND ESTADO             = 'Activo'
    ),
    'ESTADOS_SERVICIO',
    'In-Corte',
    NULL,
    NULL,
    NULL,
    'Activo',
    'jcandelario',
    SYSDATE,
    '172.17.0.1',
    'jcandelario',
    SYSDATE,
    '172.17.0.1',
    NULL,
    '18',
    NULL,
    NULL,
    NULL
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
    VALOR6,
    VALOR7,
    OBSERVACION
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (
      SELECT ID_PARAMETRO
      FROM DB_GENERAL.ADMI_PARAMETRO_CAB
      WHERE NOMBRE_PARAMETRO = 'PROCESO_EMER_SANITARIA'
      AND ESTADO             = 'Activo'
    ),
    'ESTADOS_SERVICIO',
    'Trasladado',
    NULL,
    NULL,
    NULL,
    'Activo',
    'jcandelario',
    SYSDATE,
    '172.17.0.1',
    'jcandelario',
    SYSDATE,
    '172.17.0.1',
    NULL,
    '18',
    NULL,
    NULL,
    NULL
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
    VALOR6,
    VALOR7,
    OBSERVACION
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (
      SELECT ID_PARAMETRO
      FROM DB_GENERAL.ADMI_PARAMETRO_CAB
      WHERE NOMBRE_PARAMETRO = 'PROCESO_EMER_SANITARIA'
      AND ESTADO             = 'Activo'
    ),
    'MES_DIFERIDO',
    '3',
    NULL,
    NULL,
    NULL,
    'Activo',
    'jcandelario',
    SYSDATE,
    '172.17.0.1',
    'jcandelario',
    SYSDATE,
    '172.17.0.1',
    NULL,
    '18',
    NULL,
    NULL,
    NULL
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
    VALOR6,
    VALOR7,
    OBSERVACION
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (
      SELECT ID_PARAMETRO
      FROM DB_GENERAL.ADMI_PARAMETRO_CAB
      WHERE NOMBRE_PARAMETRO = 'PROCESO_EMER_SANITARIA'
      AND ESTADO             = 'Activo'
    ),
    'MES_DIFERIDO',
    '6',
    NULL,
    NULL,
    NULL,
    'Activo',
    'jcandelario',
    SYSDATE,
    '172.17.0.1',
    'jcandelario',
    SYSDATE,
    '172.17.0.1',
    NULL,
    '18',
    NULL,
    NULL,
    NULL
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
    VALOR6,
    VALOR7,
    OBSERVACION
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (
      SELECT ID_PARAMETRO
      FROM DB_GENERAL.ADMI_PARAMETRO_CAB
      WHERE NOMBRE_PARAMETRO = 'PROCESO_EMER_SANITARIA'
      AND ESTADO             = 'Activo'
    ),
    'MES_DIFERIDO',
    '12',
    NULL,
    NULL,
    NULL,
    'Activo',
    'jcandelario',
    SYSDATE,
    '172.17.0.1',
    'jcandelario',
    SYSDATE,
    '172.17.0.1',
    NULL,
    '18',
    NULL,
    NULL,
    NULL
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
    VALOR6,
    VALOR7,
    OBSERVACION
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (
      SELECT ID_PARAMETRO
      FROM DB_GENERAL.ADMI_PARAMETRO_CAB
      WHERE NOMBRE_PARAMETRO = 'PROCESO_EMER_SANITARIA'
      AND ESTADO             = 'Activo'
    ),
    'VALOR_FACT_MIN',
    '8',
    NULL,
    NULL,
    NULL,
    'Activo',
    'jcandelario',
    SYSDATE,
    '172.17.0.1',
    'jcandelario',
    SYSDATE,
    '172.17.0.1',
    NULL,
    '18',
    NULL,
    NULL,
    NULL
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
    VALOR6,
    VALOR7,
    OBSERVACION
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (
      SELECT ID_PARAMETRO
      FROM DB_GENERAL.ADMI_PARAMETRO_CAB
      WHERE NOMBRE_PARAMETRO = 'PROCESO_EMER_SANITARIA'
      AND ESTADO             = 'Activo'
    ),
    'VALOR_FORMAS_DE_PAGO',
    '1,2,3,11',
    'EFECTIVO,CHEQUE,DEBITO BANCARIO,RECAUDACION',
    NULL,
    NULL,
    'Activo',
    'jcandelario',
    SYSDATE,
    '172.17.0.1',
    'jcandelario',
    SYSDATE,
    '172.17.0.1',
    NULL,
    '18',
    NULL,
    NULL,
    NULL
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
    VALOR6,
    VALOR7,
    OBSERVACION
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (
      SELECT ID_PARAMETRO
      FROM DB_GENERAL.ADMI_PARAMETRO_CAB
      WHERE NOMBRE_PARAMETRO = 'PROCESO_EMER_SANITARIA'
      AND ESTADO             = 'Activo'
    ),
    'FACTURAS_MINIMA',
    '3',
    NULL,
    NULL,
    NULL,
    'Activo',
    'jcandelario',
    SYSDATE,
    '172.17.0.1',
    'jcandelario',
    SYSDATE,
    '172.17.0.1',
    NULL,
    '18',
    NULL,
    NULL,
    NULL
  );

INSERT
INTO
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
    'Reporte Previo de Diferidos por Emergencia Sanitaria',    
    'RPT_EMER_SANIT',
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
    En el presente mail se adjunta Reporte Previo de Diferidos por Emergencia Sanitaria. Alguna novedad favor notificar a Sistemas.
    </td></tr>
    <tr><td colspan="2"><hr /></td></tr>
    </table>
</td></tr>
<tr><td></td></tr></table></body></html>
',
    'Activo',
    SYSDATE,
    'jcandelario'
  );

INSERT
INTO
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
      WHERE VALOR      = 'ssalazar@netlife.net.ec'
      AND ESTADO       IN ('Activo','Modificado')
      AND EMPRESA_COD  = '18'    
    ),
    (
      SELECT ID_PLANTILLA
      FROM DB_COMUNICACION.ADMI_PLANTILLA
      WHERE CODIGO = 'RPT_EMER_SANIT'
      AND ESTADO   = 'Activo'
    ),
    'Activo',
    SYSDATE,
    'jcandelario',
    'NO'
  );

INSERT
INTO
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
      WHERE VALOR      = 'dbravo@netlife.net.ec'
      AND ESTADO       IN ('Activo','Modificado')
      AND EMPRESA_COD  = '18'    
    ),
    (
      SELECT ID_PLANTILLA
      FROM DB_COMUNICACION.ADMI_PLANTILLA
      WHERE CODIGO = 'RPT_EMER_SANIT'
      AND ESTADO   = 'Activo'
    ),
    'Activo',
    SYSDATE,
    'jcandelario',
    'NO'
  );

INSERT
INTO
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
    'Reporte Previo de Creación de Solicitudes por Emergencia Sanitaria',    
    'RPT_PREV_SOL_ES',
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
    En el presente mail se adjunta Reporte Previo de Creacion de Solicitudes de Diferidos por Emergencia Sanitaria. Alguna novedad favor notificar a Sistemas.
    </td></tr>
    <tr><td colspan="2"><hr /></td></tr>
    </table>
</td></tr>
<tr><td></td></tr></table></body></html>
',
    'Activo',
    SYSDATE,
    'jcandelario'
  );

INSERT
INTO
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
      WHERE VALOR      = 'ssalazar@netlife.net.ec'
      AND ESTADO       IN ('Activo','Modificado')
      AND EMPRESA_COD  = '18'    
    ),
    (
      SELECT ID_PLANTILLA
      FROM DB_COMUNICACION.ADMI_PLANTILLA
      WHERE CODIGO = 'RPT_PREV_SOL_ES'
      AND ESTADO   = 'Activo'
    ),
    'Activo',
    SYSDATE,
    'jcandelario',
    'NO'
  );

INSERT
INTO
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
      WHERE VALOR      = 'dbravo@netlife.net.ec'
      AND ESTADO       IN ('Activo','Modificado')
      AND EMPRESA_COD  = '18'    
    ),
    (
      SELECT ID_PLANTILLA
      FROM DB_COMUNICACION.ADMI_PLANTILLA
      WHERE CODIGO = 'RPT_PREV_SOL_ES'
      AND ESTADO   = 'Activo'
    ),
    'Activo',
    SYSDATE,
    'jcandelario',
    'NO'
  );

INSERT
INTO
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
    'Reporte Final de Creación de Solicitudes por Emergencia Sanitaria',    
    'RPT_FIN_SOL_ES',
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
    En el presente mail se adjunta Reporte Final de Creacion de Solicitudes de Diferidos por Emergencia Sanitaria. Alguna novedad favor notificar a Sistemas.
    </td></tr>
    <tr><td colspan="2"><hr /></td></tr>
    </table>
</td></tr>
<tr><td></td></tr></table></body></html>
',
    'Activo',
    SYSDATE,
    'jcandelario'
  );

INSERT
INTO
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
      WHERE VALOR      = 'ssalazar@netlife.net.ec'
      AND ESTADO       IN ('Activo','Modificado')
      AND EMPRESA_COD  = '18'    
    ),
    (
      SELECT ID_PLANTILLA
      FROM DB_COMUNICACION.ADMI_PLANTILLA
      WHERE CODIGO = 'RPT_FIN_SOL_ES'
      AND ESTADO   = 'Activo'
    ),
    'Activo',
    SYSDATE,
    'jcandelario',
    'NO'
  );

INSERT
INTO
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
      WHERE VALOR      = 'dbravo@netlife.net.ec'
      AND ESTADO       IN ('Activo','Modificado')
      AND EMPRESA_COD  = '18'    
    ),
    (
      SELECT ID_PLANTILLA
      FROM DB_COMUNICACION.ADMI_PLANTILLA
      WHERE CODIGO = 'RPT_FIN_SOL_ES'
      AND ESTADO   = 'Activo'
    ),
    'Activo',
    SYSDATE,
    'jcandelario',
    'NO'
  );

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
    DB_COMERCIAL.SEQ_ADMI_CARACTERISTICA.NEXTVAL,
    'ES_SOL_FACTURA',
    'T',
    'Activo',
    SYSDATE,
    'jcandelario',
    NULL,
    NULL,
    'FINANCIERO'
  );

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
    DB_COMERCIAL.SEQ_ADMI_CARACTERISTICA.NEXTVAL,
    'ES_MESES_DIFERIDO',
    'T',
    'Activo',
    SYSDATE,
    'jcandelario',
    NULL,
    NULL,
    'FINANCIERO'
  );

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
    DB_COMERCIAL.SEQ_ADMI_CARACTERISTICA.NEXTVAL,
    'ES_PROCESO_MASIVO',
    'T',
    'Activo',
    SYSDATE,
    'jcandelario',
    NULL,
    NULL,
    'FINANCIERO'
  );

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
    DB_COMERCIAL.SEQ_ADMI_CARACTERISTICA.NEXTVAL,
    'ES_ID_SOLICITUD',
    'N',
    'Activo',
    SYSDATE,
    'apenaherrera',
    NULL,
    NULL,
    'FINANCIERO'
  );

INSERT
INTO DB_COMERCIAL.ADMI_TIPO_SOLICITUD
  (
    ID_TIPO_SOLICITUD,
    DESCRIPCION_SOLICITUD,
    FE_CREACION,
    USR_CREACION,
    FE_ULT_MOD,
    USR_ULT_MOD,
    ESTADO,
    TAREA_ID,
    ITEM_MENU_ID,
    PROCESO_ID
  )
  VALUES
  (
    DB_COMERCIAL.SEQ_ADMI_TIPO_SOLICITUD.NEXTVAL,
    'SOLICITUD DIFERIDO DE FACTURA POR EMERGENCIA SANITARIA',
    SYSDATE,
    'jcandelario',
    NULL,
    NULL,
    'Activo',
    NULL,
    NULL,
    NULL
  );
 
COMMIT;
/
