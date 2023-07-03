-- PLANTILLA DE FORMULARIO
-- PLA 1 - Nueva plantilla para correo de formulario de soporte
INSERT INTO DB_COMUNICACION.ADMI_PLANTILLA
(
  ID_PLANTILLA, NOMBRE_PLANTILLA, CODIGO, MODULO, PLANTILLA, ESTADO,
  FE_CREACION, USR_CREACION, FE_ULT_MOD, USR_ULT_MOD, EMPRESA_COD
)
VALUES 
(
    DB_COMUNICACION.SEQ_ADMI_PLANTILLA.NEXTVAL,
    'Goltv formulario soporte','GOLTV-FORML2','TECNICO',
    '<html><head>
<meta http-equiv=Content-Type content="text/html; charset=UTF-8">
</head><body>
<table align="center" width="100%" cellspacing="0" cellpadding="5">
<tr>
<td align="center" style="border:1px solid #6699CC;background-color:#E5F2FF;">
<img alt=""  src="http://images.telconet.net/others/telcos/logo.png"/>
</td></tr><tr>
<td style="border:1px solid #6699CC;">
<table width="100%" cellspacing="0" cellpadding="5">
<tr><td colspan="2">Estimado personal,</td></tr>
<tr><td colspan="2">
El presente correo es para informarle que se creó un formulario de reporte en el cual se detallan posibles daños en la plataforma {{ nombreProducto }} de acuerdo con lo indicado por 					el cliente.
</td></tr><tr><td colspan="2"><hr /></td></tr><tr>
<td colspan="2" style="text-align: center;">
<strong>Información del Ticket</strong>
</td></tr><tr><td colspan="2"><hr /></td></tr>
<tr><td><strong>Numero de ticket o identificador de Netlife:</strong></td><td>{{ numeroTicket }}</td></tr>
<tr><td><strong>Contratación del servicio:</strong></td><td>{{ contratoServicio }}</td></tr>
<tr><td><strong>Nombre completo del cliente:</strong></td><td>{{ nombreCliente }}</td></tr>
<tr><td><strong>Correo:</strong></td><td>{{ correo }}</td></tr>
<tr><td><strong>Celular:</strong></td><td>{{ celular }}</td></tr>
<tr><td><strong>Plan contratado:</strong></td><td>{{ planContratado }}</td></tr>
<tr><td><strong>Recurrente o no recurrente:</strong></td><td>{{ recurrente }}</td></tr>
<tr><td><strong>País:</strong></td><td>{{ pais }}</td></tr>
<tr><td colspan="2"><hr /></td></tr><tr>
<td colspan="2" style="text-align: center;">
<strong>Resumen del Problema</strong>
</td></tr><tr><td colspan="2"><hr /></td></tr>
<tr><td><strong>Contenido:</strong></td><td><p>{{ contenido }}</p></td></tr>
<tr><td><strong>Dispositivo:</strong></td><td>{{ dispositivo }}</td></tr>
<tr><td><strong>Resumen del problema:</strong></td><td><p>{{ resumen }}</p></td></tr>
<tr><td colspan="2"><br/></td></tr>
</table></td></tr><tr><td></td></tr>
<tr><td><strong><font size="2" face="Tahoma">MegaDatos S.A.</font></strong></td></tr>
</table></body></html>',
    'Activo',sysdate,'djreyes',null,null,18
);

COMMIT;
/
