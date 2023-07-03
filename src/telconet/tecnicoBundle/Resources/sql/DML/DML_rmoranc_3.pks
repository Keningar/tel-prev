--Actualizando Plantillas de correo enviado a cliente en Instalación TN
SET SERVEROUTPUT ON 200000;
declare
    bada clob:='<!DOCTYPE html>';
begin

DBMS_LOB.APPEND(bada, '<html>
<head>
    <meta http-equiv=Content-Type content="text/html; charset=utf-8">
<style type="text/css">
.negrita {
    font-weight: bold;
}
</style>
</head>
<body topmargin="0" style="width:600px; padding: 20px;">
    <table border=0 cellspacing=0 cellpadding=0 style="width: 600px;">
        <tr>
            <td>
                <div id="image-top" align="center">
                    <img src="http://images.telconet.net/header_instalacion_servicio.jpg" style="width:100%;">
                </div>
            </td>
        </tr>
        <tr>
            <td>
                <table border="0" cellspacing="0" cellpadding="0" align="center"
                       style="border-collapse:collapse;border:none; width:550px">
                    <tr>
                        <td valign="top">
                          <p class="negrita" style="margin-right:30.8pt;text-align:justify;mso-height-rule:exactly">

                                    Estimado/a {{ cliente }}

                                </p>

                            <p style="margin-right:0.8pt;text-align:justify;mso-height-rule:exactly"><span
                                    style="font-size:10.0pt;font-family:"Arial","sans-serif";mso-fareast-font-family:
                                    "Times New Roman"">
                                        Bienvenido al Selecto Grupo de Clientes de Telconet, es un honor contar con su presencia y hacer de nuestra relación comercial una experiencia totalmente agradable, a fin de convertirnos en un futuro cercano, en el Socio Estratégico Tecnológico de las actividades que realice su prestigiosa Compañía.
                            </span></p>
                            <p style="margin-right:0.8pt;text-align:justify;mso-height-rule:exactly"><span
                                    style="font-size:10.0pt;font-family:"Arial","sans-serif";mso-fareast-font-family:
                                    "Times New Roman"">La instalación de su servicio ha sido realizada con éxito, en el correo se encuentra adjunta el Acta de Entrega de Última Milla del servicio contratado..
                              </span></p>
                            <p style="margin-right:0.8pt;text-align:justify;mso-height-rule:exactly"><span
                                    style="font-size:10.0pt;font-family:"Arial","sans-serif";mso-fareast-font-family:
                                    "Times New Roman"">Datos de contacto de nuestro servicio de Soporte Técnico 24/7:
                              </span></p>
                            <p style="margin-right:0.8pt;text-align:justify;mso-height-rule:exactly"><span class="negrita"
                                    style="font-size:10.0pt;font-family:"Arial","sans-serif";mso-fareast-font-family:
                                    "Times New Roman"">Email</span><span class="negrita"
                                    style="font-size:10.0pt;font-family:"Arial","sans-serif";mso-fareast-font-family:
                                    "Times New Roman""></span><span
                                    style="font-size:10.0pt;font-family:"Arial","sans-serif";mso-fareast-font-family:
                                    "Times New Roman"">: soporte@telconet.ec </span></p>
                            <p style="margin-right:30.8pt;text-align:justify;mso-height-rule:exactly"><span class="negrita"
                                    style="font-size:10.0pt;font-family:"Arial","sans-serif";mso-fareast-font-family:
                                    "Times New Roman"">Teléfonos</span><span class="negrita"
                                    style="font-size:10.0pt;font-family:"Arial","sans-serif";mso-fareast-font-family:
                                    "Times New Roman""></span><span
                                    style="font-size:10.0pt;font-family:"Arial","sans-serif";mso-fareast-font-family:
                                    "Times New Roman"">: 04 602 0650 / 042 680 555 Ext.: 8000 - 8001 </span></p>
                            <p style="margin-right:0.8pt;text-align:justify;mso-height-rule:exactly"><span
                                    style="font-size:10.0pt;font-family:"Arial","sans-serif";mso-fareast-font-family:
                                    "Times New Roman""><span class="negrita">Si desea conocer nuestras Soluciones Corporativas visite nuestro website: www.telconet.ec</span></span></p>
                          <p style="margin-right:0.8pt;text-align:justify;mso-height-rule:exactly"><span
                                    style="font-size:10.0pt;font-family:"Arial","sans-serif";mso-fareast-font-family:
                                    "Times New Roman"">En caso de cualquier novedad no dude en contactarnos al correo electrónico: soporte@telconet.ec </span></a></p>


                            <p class=MsoNormal style="text-align:justify;mso-element:frame;mso-element-frame-hspace:
                               7.05pt;mso-element-wrap:around;mso-element-anchor-horizontal:margin;
                               mso-element-top:-11.25pt;mso-height-rule:exactly"><span style="font-size:
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

UPDATE 
	DB_COMUNICACION.ADMI_PLANTILLA 
SET 
	PLANTILLA = bada
where CODIGO = 'INS-CLI-COR-TN';
commit;
end;
/

