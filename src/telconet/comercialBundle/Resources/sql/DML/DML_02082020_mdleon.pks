
Insert Into  DB_COMUNICACION.ADMI_PLANTILLA
(Id_plantilla,Nombre_Plantilla,Codigo,Modulo,Plantilla,Estado,Fe_creacion,Usr_creacion,Empresa_cod)
Values
(DB_COMUNICACION.SEQ_ADMI_PLANTILLA.nextval,
'Notificación de Finalizacion en el servicio de Seguridad',
'SEGURIDAD_SDWAN',
'COMERCIAL',
'<html>
  <head>
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
		    Estimados(as) usuarios(as):<br/><br/>
			El producto {{producto}} con login {{login}} caduca el {{fecha}}.<br/><br/>	
		    <br/><br/>
		    Por favor tomar en cuenta para los fines consiguientes.<br/><br/>
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
'mdleon',
10);

COMMIT;

/
