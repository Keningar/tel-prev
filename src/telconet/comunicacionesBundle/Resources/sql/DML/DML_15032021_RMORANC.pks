--PLANTILLA DE CORREO PARA ENVIÓ AUTOMÁTICO QUE SE UTILIZARÁ EN P_PROCESA_ACT_COORDENADA_MOVIL
Insert Into  DB_COMUNICACION.ADMI_PLANTILLA
(Id_plantilla,Nombre_Plantilla,Codigo,Modulo,Plantilla,Estado,Fe_creacion,Usr_creacion,Empresa_cod)
Values
(DB_COMUNICACION.SEQ_ADMI_PLANTILLA.nextval,
'Notificación Automática por factibilidad no gestionada.',
'MAIL_FACT_JOBTM',
'COMERCIAL',
'<html>
  <head>
        <meta http-equiv=Content-Type content="text/html; charset=UTF-8">
  </head>
  <body>
    <table width=''100%'' border=''0'' bgcolor=''#ffffff''>
      <tr>
	<td>
	      <table width=''auto'' style=''border:1px solid #000000;border-color:#A9E2F3;'' cellpadding="10">
		<tr>
		    <td align=''center'' style=''background-color:#e5f2ff;border:1px solid #000000;border-color:#A9E2F3;''>
			  <img src=''http://images.telconet.net/others/sit/notificaciones/logo-tn.png''/>
		    </td>
		</tr>		
	      <tr>
		  <td style=''border:1px solid #000000;border-color:#A9E2F3;''>
		    Estimados(as):<br/><br/>
			No se ha gestionado la factibilidad al login {{login}} en un tiempo mayor a {{minutos}} minutos.<br/><br/>	
		    <br/><br/>
		    Por favor tomar en cuenta para los fines pertinentes.<br/><br/>
		    Atentamente,<br/><br/>
		    ----------------------------------------
		    <br/><br/>
		    <strong>TELCONET S.A.</strong>		      
		  </td>
		</tr>		
	    </table>
	</td>
      </tr>
    </table>
  </body>
</html>',
'Activo',
SYSDATE,
'rmoranc',
18);


COMMIT;


