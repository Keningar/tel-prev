--INGRESANDO NUEVA PLANTILLA DE CORREO SOPORTE TN CON CONFIRMACION DEL ENLACE.
SET SERVEROUTPUT ON 200000;
declare
    bada clob:='<!DOCTYPE html>';
begin

DBMS_LOB.APPEND(bada, '
<html>
<head>
    <meta http-equiv=Content-Type content="text/html; charset=utf-8">
<style type="text/css">
.negrita {
	font-weight: bold;
}
.texto-titulo {
	font-family: Arial, Helvetica, sans-serif;
}
</style>
</head>

<body topmargin="0" style="width:600px; padding: 20px;">
	<table border=0 cellspacing=0 cellpadding=0 style="width: 600px;">
		<tr>
			<td>
				<div id="image-top" align="center">
					<img src="http://images.telconet.net/header_gestion_calidad.jpg" style="width:100%;">
				</div>
			</td>
        </tr>
        <tr>
            <td>    
				<table border="0" cellspacing="0" cellpadding="0" align="center"
                       style="border-collapse:collapse;border:none; width:550px">
                    <tr>
                        <td valign="top">
							<p class="texto-titulo" style="margin-right:30.8pt;text-align:justify;mso-height-rule:exactly">
								Estimado/a {{ cliente }}
							</p>

							<p style="margin-right:0.8pt;text-align:justify;mso-height-rule:exactly">
								<span style="font-size:10.0pt;font-family:"Arial","sans-serif";mso-fareast-font-family: "Times New Roman"">
									Comunicamos que el Soporte Técnico requerido por usted fue realizado con éxito. 
									<br>
									<br>
									
									<b style="color:#155c94;">  Hemos probado su enlace y se encuentra operativo.</b>
									
									<br>
									<br>
									<b style="margin-left: 70px;">Número de paquetes enviados:</b> 	{{ paquetesEnviados }}  <br>
									<b style="margin-left: 70px;">Número de paquetes recibidos:</b> {{ paquetesRecibidos }}  <br>
									<b style="margin-left: 70px;;">Latencia media:</b> 				{{ latenciaMedia }}  
									<br>
									<br>
									
									Adjuntamos acta de Visita Técnica del servicio realizado.
								
								</span>
							</p>
							<p style="margin-right:0.8pt;text-align:justify;mso-height-rule:exactly"><span
										style="font-size:10.0pt;font-family:"Arial","sans-serif";mso-fareast-font-family:
										"Times New Roman"">En caso de cualquier novedad con el acta de Visita Técnica adjunta, por favor no dude en contactarnos al correo electrónico: soporte@telconet.ec</span></p>
							<p style="margin-right:0.8pt;text-align:justify;mso-height-rule:exactly"><span style="font-size:
                                10.0pt;font-family:"Arial","sans-serif";mso-fareast-font-family:"Times New Roman"">Atentamente, <br>
                  </span><span style="font-size:10.0pt; font-family:"Arial","sans-serif"; font-weight: bold;">Telconet
                  </span></p>
						</td>
					</tr>
				</table>
			</td>
        </tr>
        <tr>
            <td>
				<div id="image-down">
					<img src="http://images.telconet.net/footer_tn.jpg" style="width:100%;">
				</div>
			</td>
        </tr>
	</table>
</body>
</html>');

dbms_output.put_line('The length of the manipulated LOB is '||dbms_lob.getlength(bada));

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
    'Correo Cliente Soporte TN Confirmacion de enlace',    
    'SOP-CLI-CORTNCE',
    'TECNICO',
    bada,
    'Activo',
    SYSDATE,
    'rmoranc'
  );
end;


COMMIT;


