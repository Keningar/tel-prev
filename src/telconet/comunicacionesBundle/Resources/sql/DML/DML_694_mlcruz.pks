SET DEFINE OFF 
UPDATE DB_COMUNICACION.ADMI_PLANTILLA 
SET PLANTILLA =
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
                            <td colspan="2"><strong>ESTIMADO CLIENTE:</strong></td>
                        </tr>
                        <tr>
                            <td colspan="2">
A trav&eacute;s del presente tenemos a bien saludarlo y recordarle que los pagos
deben efectuarse dentro de los 5 d&iacute;as posteriores a la recepci&oacute;n de la
factura (incluye retenci&oacute;n del mes corriente); sin embargo el sistema a la
presente fecha presenta <strong>saldos pendientes</strong>; por lo tanto, se le informa que el
d&iacute;a 21 el sistema proceder&aacute; de manera autom&aacute;tica a la <strong>suspensi&oacute;n del
servicio</strong> de las cuentas que no tengan registrada la totalidad del pago;
ejecut&aacute;ndose la reactivaci&oacute;n autom&aacute;ticamente con el ingreso al sistema de los
respectivos pago y retenci&oacute;n. 
                            </td>
                        </tr>
			<tr>
                            <td colspan="2"></td>
                        </tr>
			<tr>
                            <td colspan="2"></td>
                        </tr>
                        <tr>
                            <td>
                                <strong>Cliente:</strong>
                            </td>
                            <td>{{ NOMBRES_CLIENTE }}</td>
                        </tr>
                        <tr>
                            <td>
                                <strong>Login de Facturaci&oacute;n:</strong>
                            </td>
                            <td>{{ LOGIN }}</td>
                        </tr>
                        <tr>
                            <td>
                                <strong># de Puntos que se podr&iacute;an ver afectados con la suspensi&oacute;n:</strong>
                            </td>
                            <td>{{ NUM_PUNTOS_AFECTADOS }}</td>
                        </tr>
                        <tr>
                            <td>
                                <strong>VALOR PENDIENTE DE PAGO:</strong>
                            </td>
                            <td>{{ SALDO_PUNTO_FACT }}</td>
                        </tr>
                        <tr>
                            <td colspan="2"></td>
                        </tr>
                        <tr>
                            <td colspan="2"></td>
                        </tr>') ||
                        TO_CLOB('
                        <tr>
                            <td colspan="2">
Si el pago lo efect&uacute;a a trav&eacute;s de una de las cuentas de Telconet, debe
remitir la respectiva papeleta al Departamento de Cobranzas para su
oportuno registro en el Sistema, haciendo constar n&uacute;mero de factura
a la cual aplica el pago.
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <p>Novedades con los pagos comunicarlos a: <br> <u style="color: blue;">cobranzas_gye@telconet.ec</u> - <strong>PBX 6020650</strong></p>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <table  align="center" width="100%" cellspacing="0" cellpadding="5" border="1" style="margin:5px;">
				    <tr>
                            	        <td>ext 5811 Ivonne Garcia</td>
					<td><u style="color: blue;">iegarcia@telconet.ec</u></td>
					<td>&nbsp;</td>
					<td>ext 5821 Lissette Perez</td>
					<td><u style="color: blue;">sperez@telconet.ec</u></td>
                        	    </tr>
				    <tr>
                            	        <td>ext 5815 Bengie Llerena</td>
					<td><u style="color: blue;">bllerena@telconet.ec</u></td>
					<td>&nbsp;</td>
					<td>ext 5823 Rita Povea</td>
					<td><u style="color: blue;">rpovea@telconet.ec</u></td>
                        	    </tr>
				    <tr>
                            	        <td>ext 5817 Marlene Chamba</td>
					<td><u style="color: blue;">mdchamba@telconet.ec</u></td>
					<td>&nbsp;</td>
					<td>ext 5801 Mar&iacute;a Elena Franco</td>
					<td><u style="color: blue;">mfranco@telconet.ec</u></td>
                        	    </tr>
				    <tr>
                            	        <td>ext 5819 Nathaly Cueva</td>
					<td><u style="color: blue;">ncueva@telconet.ec</u></td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
                        	    </tr>
				</table>
                            </td>
                        </tr>
                        
                    </table>
                </td>
            </tr>
            <tr>
                <td>
                    &nbsp;
                </td>
            </tr>


	   <tr>
            	<td colspan="2">
        		<p><font size="2" face="Tahoma">Agradecemos su pronto pago evitando as&iacute; las molestias ocasionadas
por la suspensi&oacute;n.</p>
            	</td>
            </tr>
	   <tr>
            	<td colspan="2">
        		<p><strong><font size="2" face="Tahoma">NOTA IMPORTANTE: "Favor no considerar el presente
recordatorio si al momento de recibirlo Ud. ya efectu&oacute; su
respectivo pago".</strong></p>
            	</td>
            </tr>
            <tr>
            	<td colspan="2">
                	<p><strong><font size="2" face="Tahoma">Atentamente,</font></strong></p>
            	</td>
            </tr>
        	<tr>
            	<td colspan="2">
                	<p><strong><font size="2" face="Tahoma">Departamento de Cobranzas,<br>Telconet S.A.</font></strong></p>
            	</td>
            </tr> 
        </table>
    </body>
</html>') 
WHERE CODIGO = 'AVISOPAGO_TNGYE';



UPDATE DB_COMUNICACION.ADMI_PLANTILLA 
SET PLANTILLA =
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
                            <td colspan="2"><strong>ESTIMADO CLIENTE:</strong></td>
                        </tr>
                        <tr>
                            <td colspan="2">
A trav&eacute;s del presente tenemos a bien saludarlo y recordarle que los pagos
deben efectuarse dentro de los 5 d&iacute;as posteriores a la recepci&oacute;n de la
factura (incluye retenci&oacute;n del mes corriente); sin embargo el sistema a la
presente fecha presenta <strong>saldos pendientes</strong>; por lo tanto, se le informa que el
d&iacute;a 21 el sistema proceder&aacute; de manera autom&aacute;tica a la <strong>suspensi&oacute;n del
servicio</strong> de las cuentas que no tengan registrada la totalidad del pago;
ejecut&aacute;ndose la reactivaci&oacute;n autom&aacute;ticamente con el ingreso al sistema de los
respectivos pago y retenci&oacute;n. 
                            </td>
                        </tr>
			<tr>
                            <td colspan="2"></td>
                        </tr>
			<tr>
                            <td colspan="2"></td>
                        </tr>
                        <tr>
                            <td>
                                <strong>Cliente:</strong>
                            </td>
                            <td>{{ NOMBRES_CLIENTE }}</td>
                        </tr>
                        <tr>
                            <td>
                                <strong>Login de Facturaci&oacute;n:</strong>
                            </td>
                            <td>{{ LOGIN }}</td>
                        </tr>
                        <tr>
                            <td>
                                <strong># de Puntos que se podr&iacute;an ver afectados con la suspensi&oacute;n:</strong>
                            </td>
                            <td>{{ NUM_PUNTOS_AFECTADOS }}</td>
                        </tr>
                        <tr>
                            <td>
                                <strong>VALOR PENDIENTE DE PAGO:</strong>
                            </td>
                            <td>{{ SALDO_PUNTO_FACT }}</td>
                        </tr>
                        <tr>
                            <td colspan="2"></td>
                        </tr>
                        <tr>
                            <td colspan="2"></td>
                        </tr>') ||
                        TO_CLOB('
                        <tr>
                            <td colspan="2">
Si el pago lo efect&uacute;a a trav&eacute;s de una de las cuentas de Telconet, debe
remitir la respectiva papeleta al Departamento de Cobranzas para su
oportuno registro en el Sistema, haciendo constar n&uacute;mero de factura
a la cual aplica el pago.
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <p>Novedades con los pagos comunicarlos a: <br> <u style="color: blue;">cobranzas_uio@telconet.ec</u> - <strong>PBX 02-3963100</strong></p>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="1">
                                <table  align="center" width="100%" cellspacing="0" cellpadding="5" border="1" style="margin:5px;">
				    <tr>
                            	        <td>ext 2901 Irene Molina</td>
                        	    </tr>
                                    <tr>
					<td>ext 2911 Mar&iacute;a Fernanda Villarreal</td>
                        	    </tr>
				</table>
                            </td>
                            <td colspan="1"></td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td>
                    &nbsp;
                </td>
            </tr>


	   <tr>
            	<td colspan="2">
        		<p><font size="2" face="Tahoma">Agradecemos su pronto pago evitando as&iacute; las molestias ocasionadas
por la suspensi&oacute;n.</p>
            	</td>
            </tr>
	   <tr>
            	<td colspan="2">
        		<p><strong><font size="2" face="Tahoma">NOTA IMPORTANTE: "Favor no considerar el presente
recordatorio si al momento de recibirlo Ud. ya efectu&oacute; su
respectivo pago".</strong></p>
            	</td>
            </tr>
            <tr>
            	<td colspan="2">
                	<p><strong><font size="2" face="Tahoma">Atentamente,</font></strong></p>
            	</td>
            </tr>
        	<tr>
            	<td colspan="2">
                	<p><strong><font size="2" face="Tahoma">Departamento de Cobranzas,<br>Telconet S.A.</font></strong></p>
            	</td>
            </tr> 
        </table>
    </body>
</html>') 
WHERE CODIGO = 'AVISOPAGO_TNUIO';


UPDATE DB_COMUNICACION.ADMI_PLANTILLA 
SET PLANTILLA =
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
                            <td colspan="2"><strong>ESTIMADO CLIENTE:</strong></td>
                        </tr>
                        <tr>
                            <td colspan="2">
A trav&eacute;s del presente tenemos a bien saludarlo y recordarle que los pagos
deben efectuarse dentro de los 5 d&iacute;as posteriores a la recepci&oacute;n de la
factura (incluye retenci&oacute;n del mes corriente); sin embargo el sistema a la
presente fecha presenta <strong>saldos pendientes</strong>; por lo tanto, se le informa que el
d&iacute;a 21 el sistema proceder&aacute; de manera autom&aacute;tica a la <strong>suspensi&oacute;n del
servicio</strong> de las cuentas que no tengan registrada la totalidad del pago;
ejecut&aacute;ndose la reactivaci&oacute;n autom&aacute;ticamente con el ingreso al sistema de los
respectivos pago y retenci&oacute;n. 
                            </td>
                        </tr>
			<tr>
                            <td colspan="2"></td>
                        </tr>
			<tr>
                            <td colspan="2"></td>
                        </tr>
                        <tr>
                            <td>
                                <strong>Cliente:</strong>
                            </td>
                            <td>{{ NOMBRES_CLIENTE }}</td>
                        </tr>
                        <tr>
                            <td>
                                <strong>Login de Facturaci&oacute;n:</strong>
                            </td>
                            <td>{{ LOGIN }}</td>
                        </tr>
                        <tr>
                            <td>
                                <strong># de Puntos que se podr&iacute;an ver afectados con la suspensi&oacute;n:</strong>
                            </td>
                            <td>{{ NUM_PUNTOS_AFECTADOS }}</td>
                        </tr>
                        <tr>
                            <td>
                                <strong>VALOR PENDIENTE DE PAGO:</strong>
                            </td>
                            <td>{{ SALDO_PUNTO_FACT }}</td>
                        </tr>
                        <tr>
                            <td colspan="2"></td>
                        </tr>
                        <tr>
                            <td colspan="2"></td>
                        </tr>') ||
                        TO_CLOB('
                        <tr>
                            <td colspan="2">
Si el pago lo efect&uacute;a a trav&eacute;s de una de las cuentas de Telconet, debe
remitir la respectiva papeleta al Departamento de Cobranzas para su
oportuno registro en el Sistema, haciendo constar n&uacute;mero de factura
a la cual aplica el pago.
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">
				<p>Novedades con los pagos comunicarlos a <u style="color: blue;">cdurazno@telconet.ec</u>, con Carmita Durazno, <strong>PBX 07-4134501</strong> ext. 5811</p>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">
                            </td>
                        </tr>
                        
                    </table>
                </td>
            </tr>
            <tr>
                <td>
                    &nbsp;
                </td>
            </tr>


	   <tr>
            	<td colspan="2">
        		<p><font size="2" face="Tahoma">Agradecemos su pronto pago evitando as&iacute; las molestias ocasionadas
por la suspensi&oacute;n.</p>
            	</td>
            </tr>
	   <tr>
            	<td colspan="2">
        		<p><strong><font size="2" face="Tahoma">NOTA IMPORTANTE: "Favor no considerar el presente
recordatorio si al momento de recibirlo Ud. ya efectu&oacute; su
respectivo pago".</strong></p>
            	</td>
            </tr>
            <tr>
            	<td colspan="2">
                	<p><strong><font size="2" face="Tahoma">Atentamente,</font></strong></p>
            	</td>
            </tr>
        	<tr>
            	<td colspan="2">
                	<p><strong><font size="2" face="Tahoma">Departamento de Cobranzas,<br>Telconet S.A.</font></strong></p>
            	</td>
            </tr> 
        </table>
    </body>
</html>') 
WHERE CODIGO = 'AVISOPAGO_TNCUE';



UPDATE DB_COMUNICACION.ADMI_PLANTILLA 
SET PLANTILLA =
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
                            <td colspan="2"><strong>ESTIMADO CLIENTE:</strong></td>
                        </tr>
                        <tr>
                            <td colspan="2">
A trav&eacute;s del presente tenemos a bien saludarlo y recordarle que los pagos
deben efectuarse dentro de los 5 d&iacute;as posteriores a la recepci&oacute;n de la
factura (incluye retenci&oacute;n del mes corriente); sin embargo el sistema a la
presente fecha presenta <strong>saldos pendientes</strong>; por lo tanto, se le informa que el
d&iacute;a 21 el sistema proceder&aacute; de manera autom&aacute;tica a la <strong>suspensi&oacute;n del
servicio</strong> de las cuentas que no tengan registrada la totalidad del pago;
ejecut&aacute;ndose la reactivaci&oacute;n autom&aacute;ticamente con el ingreso al sistema de los
respectivos pago y retenci&oacute;n. 
                            </td>
                        </tr>
			<tr>
                            <td colspan="2"></td>
                        </tr>
			<tr>
                            <td colspan="2"></td>
                        </tr>
                        <tr>
                            <td>
                                <strong>Cliente:</strong>
                            </td>
                            <td>{{ NOMBRES_CLIENTE }}</td>
                        </tr>
                        <tr>
                            <td>
                                <strong>Login de Facturaci&oacute;n:</strong>
                            </td>
                            <td>{{ LOGIN }}</td>
                        </tr>
                        <tr>
                            <td>
                                <strong># de Puntos que se podr&iacute;an ver afectados con la suspensi&oacute;n:</strong>
                            </td>
                            <td>{{ NUM_PUNTOS_AFECTADOS }}</td>
                        </tr>
                        <tr>
                            <td>
                                <strong>VALOR PENDIENTE DE PAGO:</strong>
                            </td>
                            <td>{{ SALDO_PUNTO_FACT }}</td>
                        </tr>
                        <tr>
                            <td colspan="2"></td>
                        </tr>
                        <tr>
                            <td colspan="2"></td>
                        </tr>') ||
                        TO_CLOB('
                        <tr>
                            <td colspan="2">
Si el pago lo efect&uacute;a a trav&eacute;s de una de las cuentas de Telconet, debe
remitir la respectiva papeleta al Departamento de Cobranzas para su
oportuno registro en el Sistema, haciendo constar n&uacute;mero de factura
a la cual aplica el pago.
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <p>Novedades con los pagos comunicarlos a: <strong>PBX 05-2627815</strong></p>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <table  align="center" width="100%" cellspacing="0" cellpadding="5" border="1" style="margin:5px;">
				    <tr>
                            	        <td>ext 2011 Fatima Delgado</td>
					<td><u style="color: blue;">fdelgado@telconet.ec</u></td>
					<td>&nbsp;</td>
					<td>ext 2005 Catherine Franco</td>
					<td><u style="color: blue;">cfranco@telconet.ec</u></td>
                        	    </tr>
				</table>
                            </td>
                        </tr>
                        
                    </table>
                </td>
            </tr>
            <tr>
                <td>
                    &nbsp;
                </td>
            </tr>


	   <tr>
            	<td colspan="2">
        		<p><font size="2" face="Tahoma">Agradecemos su pronto pago evitando as&iacute; las molestias ocasionadas
por la suspensi&oacute;n.</p>
            	</td>
            </tr>
	   <tr>
            	<td colspan="2">
        		<p><strong><font size="2" face="Tahoma">NOTA IMPORTANTE: "Favor no considerar el presente
recordatorio si al momento de recibirlo Ud. ya efectu&oacute; su
respectivo pago".</strong></p>
            	</td>
            </tr>
            <tr>
            	<td colspan="2">
                	<p><strong><font size="2" face="Tahoma">Atentamente,</font></strong></p>
            	</td>
            </tr>
        	<tr>
            	<td colspan="2">
                	<p><strong><font size="2" face="Tahoma">Departamento de Cobranzas,<br>Telconet S.A.</font></strong></p>
            	</td>
            </tr> 
        </table>
    </body>
</html>') 
WHERE CODIGO = 'AVISOPAGO_TNMAN';

UPDATE DB_COMUNICACION.ADMI_PLANTILLA 
SET PLANTILLA =
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
                            <td colspan="2"><strong>ESTIMADO CLIENTE:</strong></td>
                        </tr>
                        <tr>
                            <td colspan="2">
A trav&eacute;s del presente tenemos a bien saludarlo y recordarle que los pagos
deben efectuarse dentro de los 5 d&iacute;as posteriores a la recepci&oacute;n de la
factura (incluye retenci&oacute;n del mes corriente); sin embargo el sistema a la
presente fecha presenta <strong>saldos pendientes</strong>; por lo tanto, se le informa que el
d&iacute;a 21 el sistema proceder&aacute; de manera autom&aacute;tica a la <strong>suspensi&oacute;n del
servicio</strong> de las cuentas que no tengan registrada la totalidad del pago;
ejecut&aacute;ndose la reactivaci&oacute;n autom&aacute;ticamente con el ingreso al sistema de los
respectivos pago y retenci&oacute;n. 
                            </td>
                        </tr>
			<tr>
                            <td colspan="2"></td>
                        </tr>
			<tr>
                            <td colspan="2"></td>
                        </tr>
                        <tr>
                            <td>
                                <strong>Cliente:</strong>
                            </td>
                            <td>{{ NOMBRES_CLIENTE }}</td>
                        </tr>
                        <tr>
                            <td>
                                <strong>Login de Facturaci&oacute;n:</strong>
                            </td>
                            <td>{{ LOGIN }}</td>
                        </tr>
                        <tr>
                            <td>
                                <strong># de Puntos que se podr&iacute;an ver afectados con la suspensi&oacute;n:</strong>
                            </td>
                            <td>{{ NUM_PUNTOS_AFECTADOS }}</td>
                        </tr>
                        <tr>
                            <td>
                                <strong>VALOR PENDIENTE DE PAGO:</strong>
                            </td>
                            <td>{{ SALDO_PUNTO_FACT }}</td>
                        </tr>
                        <tr>
                            <td colspan="2"></td>
                        </tr>
                        <tr>
                            <td colspan="2"></td>
                        </tr>') ||
                        TO_CLOB('
                        <tr>
                            <td colspan="2">
Si el pago lo efect&uacute;a a trav&eacute;s de una de las cuentas de Telconet, debe
remitir la respectiva papeleta al Departamento de Cobranzas para su
oportuno registro en el Sistema, haciendo constar n&uacute;mero de factura
a la cual aplica el pago.
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">
				<p>Novedades con los pagos comunicarlos a <u style="color: blue;">mquirola@telconet.ec</u>, con Mario Quirola, <strong>PBX 07-2585848</strong> ext. 5801</p>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">
                            </td>
                        </tr>
                        
                    </table>
                </td>
            </tr>
            <tr>
                <td>
                    &nbsp;
                </td>
            </tr>


	   <tr>
            	<td colspan="2">
        		<p><font size="2" face="Tahoma">Agradecemos su pronto pago evitando as&iacute; las molestias ocasionadas
por la suspensi&oacute;n.</p>
            	</td>
            </tr>
	   <tr>
            	<td colspan="2">
        		<p><strong><font size="2" face="Tahoma">NOTA IMPORTANTE: "Favor no considerar el presente
recordatorio si al momento de recibirlo Ud. ya efectu&oacute; su
respectivo pago".</strong></p>
            	</td>
            </tr>
            <tr>
            	<td colspan="2">
                	<p><strong><font size="2" face="Tahoma">Atentamente,</font></strong></p>
            	</td>
            </tr>
        	<tr>
            	<td colspan="2">
                	<p><strong><font size="2" face="Tahoma">Departamento de Cobranzas,<br>Telconet S.A.</font></strong></p>
            	</td>
            </tr> 
        </table>
    </body>
</html>') 
WHERE CODIGO = 'AVISOPAGO_TNLOJ';


UPDATE DB_COMUNICACION.ADMI_PLANTILLA 
SET PLANTILLA =
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
                            <td colspan="2"><strong>ESTIMADO CLIENTE:</strong></td>
                        </tr>
                        <tr>
                            <td colspan="2">
A trav&eacute;s del presente tenemos a bien saludarlo y recordarle que los pagos
deben efectuarse dentro de los 5 d&iacute;as posteriores a la recepci&oacute;n de la
factura (incluye retenci&oacute;n del mes corriente); sin embargo el sistema a la
presente fecha presenta <strong>saldos pendientes</strong>; por lo tanto, se le informa que el
d&iacute;a 21 el sistema proceder&aacute; de manera autom&aacute;tica a la <strong>suspensi&oacute;n del
servicio</strong> de las cuentas que no tengan registrada la totalidad del pago;
ejecut&aacute;ndose la reactivaci&oacute;n autom&aacute;ticamente con el ingreso al sistema de los
respectivos pago y retenci&oacute;n. 
                            </td>
                        </tr>
			<tr>
                            <td colspan="2"></td>
                        </tr>
			<tr>
                            <td colspan="2"></td>
                        </tr>
                        <tr>
                            <td>
                                <strong>Cliente:</strong>
                            </td>
                            <td>{{ NOMBRES_CLIENTE }}</td>
                        </tr>
                        <tr>
                            <td>
                                <strong>Login de Facturaci&oacute;n:</strong>
                            </td>
                            <td>{{ LOGIN }}</td>
                        </tr>
                        <tr>
                            <td>
                                <strong># de Puntos que se podr&iacute;an ver afectados con la suspensi&oacute;n:</strong>
                            </td>
                            <td>{{ NUM_PUNTOS_AFECTADOS }}</td>
                        </tr>
                        <tr>
                            <td>
                                <strong>VALOR PENDIENTE DE PAGO:</strong>
                            </td>
                            <td>{{ SALDO_PUNTO_FACT }}</td>
                        </tr>
                        <tr>
                            <td colspan="2"></td>
                        </tr>
                        <tr>
                            <td colspan="2"></td>
                        </tr>') ||
                        TO_CLOB('
                        <tr>
                            <td colspan="2">
Si el pago lo efect&uacute;a a trav&eacute;s de una de las cuentas de Telconet, debe
remitir la respectiva papeleta al Departamento de Cobranzas para su
oportuno registro en el Sistema, haciendo constar n&uacute;mero de factura
a la cual aplica el pago.
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">
				<p>Novedades con los pagos comunicarlos a <u style="color: blue;">ivillafuerte@telconet.ec</u>, con Ines Villafuerte, <strong>PBX 05-2762652</strong> ext. 4005</p>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">
                            </td>
                        </tr>
                        
                    </table>
                </td>
            </tr>
            <tr>
                <td>
                    &nbsp;
                </td>
            </tr>


	   <tr>
            	<td colspan="2">
        		<p><font size="2" face="Tahoma>">Agradecemos su pronto pago evitando as&iacute; las molestias ocasionadas
por la suspensi&oacute;n.</p>
            	</td>
            </tr>
	   <tr>
            	<td colspan="2">
        		<p><strong><font size=>"2" face="Tahoma">NOTA IMPORTANTE: "Favor no considerar el presente
recordatorio si al momento de recibirlo Ud. ya efectu&oacute; su
respectivo pago".</strong></p>
            	</td>
            </tr>
            <tr>
            	<td colspan="2">
                	<p><strong><font size="2" face="Tahoma">Atentamente,</font></strong></p>
            	</td>
            </tr>
        	<tr>
            	<td colspan="2">
                	<p><strong><font size="2" face="Tahoma">Departamento de Cobranzas,<br>Telconet S.A.</font></strong></p>
            	</td>
            </tr> 
        </table>
    </body>
</html>') 
WHERE CODIGO = 'AVISOPAGO_TNQUE';


UPDATE DB_COMUNICACION.ADMI_PLANTILLA 
SET PLANTILLA =
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
                            <td colspan="2"><strong>ESTIMADO CLIENTE:</strong></td>
                        </tr>
                        <tr>
                            <td colspan="2">
A trav&eacute;s del presente tenemos a bien saludarlo y recordarle que los pagos
deben efectuarse dentro de los 5 d&iacute;as posteriores a la recepci&oacute;n de la
factura (incluye retenci&oacute;n del mes corriente); sin embargo el sistema a la
presente fecha presenta <strong>saldos pendientes</strong>; por lo tanto, se le informa que el
d&iacute;a 21 el sistema proceder&aacute; de manera autom&aacute;tica a la <strong>suspensi&oacute;n del
servicio</strong> de las cuentas que no tengan registrada la totalidad del pago;
ejecut&aacute;ndose la reactivaci&oacute;n autom&aacute;ticamente con el ingreso al sistema de los
respectivos pago y retenci&oacute;n. 
                            </td>
                        </tr>
			<tr>
                            <td colspan="2"></td>
                        </tr>
			<tr>
                            <td colspan="2"></td>
                        </tr>
                        <tr>
                            <td>
                                <strong>Cliente:</strong>
                            </td>
                            <td>{{ NOMBRES_CLIENTE }}</td>
                        </tr>
                        <tr>
                            <td>
                                <strong>Login de Facturaci&oacute;n:</strong>
                            </td>
                            <td>{{ LOGIN }}</td>
                        </tr>
                        <tr>
                            <td>
                                <strong># de Puntos que se podr&iacute;an ver afectados con la suspensi&oacute;n:</strong>
                            </td>
                            <td>{{ NUM_PUNTOS_AFECTADOS }}</td>
                        </tr>
                        <tr>
                            <td>
                                <strong>VALOR PENDIENTE DE PAGO:</strong>
                            </td>
                            <td>{{ SALDO_PUNTO_FACT }}</td>
                        </tr>
                        <tr>
                            <td colspan="2"></td>
                        </tr>
                        <tr>
                            <td colspan="2"></td>
                        </tr>') ||
                        TO_CLOB('
                        <tr>
                            <td colspan="2">
Si el pago lo efect&uacute;a a trav&eacute;s de una de las cuentas de Telconet, debe
remitir la respectiva papeleta al Departamento de Cobranzas para su
oportuno registro en el Sistema, haciendo constar n&uacute;mero de factura
a la cual aplica el pago.
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">
				<p>Novedades con los pagos comunicarlos a <u style="color: blue;">ebalon@telconet.ec</u>, con Edilia Balon, <strong>PBX 04-2779528</strong> ext. 2001</p>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">
                            </td>
                        </tr>
                        
                    </table>
                </td>
            </tr>
            <tr>
                <td>
                    &nbsp;
                </td>
            </tr>


	   <tr>
            	<td colspan="2">
        		<p><font size="2" face="Tahoma">Agradecemos su pronto pago evitando as&iacute; las molestias ocasionadas
por la suspensi&oacute;n.</p>
            	</td>
            </tr>
	   <tr>
            	<td colspan="2">
        		<p><strong><font size="2" face="Tahoma">NOTA IMPORTANTE: "Favor no considerar el presente
recordatorio si al momento de recibirlo Ud. ya efectu&oacute; su
respectivo pago".</strong></p>
            	</td>
            </tr>
            <tr>
            	<td colspan="2">
                	<p><strong><font size="2" face="Tahoma">Atentamente,</font></strong></p>
            	</td>
            </tr>
        	<tr>
            	<td colspan="2">
                	<p><strong><font size="2" face="Tahoma">Departamento de Cobranzas,<br>Telconet S.A.</font></strong></p>
            	</td>
            </tr> 
        </table>
    </body>
</html>') 
WHERE CODIGO = 'AVISOPAGO_TNSAL';


UPDATE DB_COMUNICACION.ADMI_PLANTILLA 
SET PLANTILLA =
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
                            <td colspan="2"><strong>ESTIMADO CLIENTE:</strong></td>
                        </tr>
                        <tr>
                            <td colspan="2">
A trav&eacute;s del presente tenemos a bien saludarlo y recordarle que los pagos
deben efectuarse dentro de los 5 d&iacute;as posteriores a la recepci&oacute;n de la
factura (incluye retenci&oacute;n del mes corriente); sin embargo el sistema a la
presente fecha presenta <strong>saldos pendientes</strong>; por lo tanto, se le informa que el
d&iacute;a 21 el sistema proceder&aacute; de manera autom&aacute;tica a la <strong>suspensi&oacute;n del
servicio</strong> de las cuentas que no tengan registrada la totalidad del pago;
ejecut&aacute;ndose la reactivaci&oacute;n autom&aacute;ticamente con el ingreso al sistema de los
respectivos pago y retenci&oacute;n. 
                            </td>
                        </tr>
			<tr>
                            <td colspan="2"></td>
                        </tr>
			<tr>
                            <td colspan="2"></td>
                        </tr>
                        <tr>
                            <td>
                                <strong>Cliente:</strong>
                            </td>
                            <td>{{ NOMBRES_CLIENTE }}</td>
                        </tr>
                        <tr>
                            <td>
                                <strong>Login de Facturaci&oacute;n:</strong>
                            </td>
                            <td>{{ LOGIN }}</td>
                        </tr>
                        <tr>
                            <td>
                                <strong># de Puntos que se podr&iacute;an ver afectados con la suspensi&oacute;n:</strong>
                            </td>
                            <td>{{ NUM_PUNTOS_AFECTADOS }}</td>
                        </tr>
                        <tr>
                            <td>
                                <strong>VALOR PENDIENTE DE PAGO:</strong>
                            </td>
                            <td>{{ SALDO_PUNTO_FACT }}</td>
                        </tr>
                        <tr>
                            <td colspan="2"></td>
                        </tr>
                        <tr>
                            <td colspan="2"></td>
                        </tr>') ||
                        TO_CLOB('
                        <tr>
                            <td colspan="2">
Si el pago lo efect&uacute;a a trav&eacute;s de una de las cuentas de Telconet, debe
remitir la respectiva papeleta al Departamento de Cobranzas para su
oportuno registro en el Sistema, haciendo constar n&uacute;mero de factura
a la cual aplica el pago.
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <p>Novedades con los pagos comunicarlos a:</p>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <table  align="center" width="100%" cellspacing="0" cellpadding="5" border="1" style="margin:5px;">
                    	            <th>SUCURSAL</th>
				    <th>CORREO</u></th>
				    <th>PBX</th>
				    <th>EXT</th>
				    <th>EJECUTIVA DE COBRANZAS</th>
				    <tr>
                            	        <td>GUAYAQUIL</td>
					<td><u style="color: blue;">cobranzas_gye@telconet.ec</u></td>
					<td>04-6020650</td>
					<td>5801</td>
					<td>Mar&iacute;a Elena Franco</td>
                        	    </tr>
				    <tr>
                            	        <td rowspan="2">QUITO</td>
					<td rowspan="2"><u style="color: blue;">cobranzas_uio@telconet.ec</u></td>
					<td rowspan="2">02-3963100</td>
					<td>2901</td>
					<td>Irene Molina</td>
                        	    </tr>
				    <tr>
					<td>2911</td>
					<td>Mar&iacute;a Fernanda Villarreal</td>
                        	    </tr>
				    <tr>
                            	        <td>CUENCA</td>
					<td><u style="color: blue;">cdurazno@telconet.ec</u></td>
					<td>07-4134501</td>
					<td>5811</td>
					<td>Carmita Durazno</td>
                        	    </tr>
				    <tr>
                            	        <td rowspan="2">MANTA</td>
					<td><u style="color: blue;">fdelgado@telconet.ec</u></td>
					<td rowspan="2">05-2627815</td>
					<td>2011</td>
					<td>Fatima Delgado</td>
                        	    </tr>
				    <tr>
                            	        <td><u style="color: blue;">cfranco@telconet.ec</u></td>
					<td>2005</td>
					<td>Catherine Franco</td>
                        	    </tr>
                                    <tr>
                            	        <td>LOJA</td>
					<td><u style="color: blue;">mquirola@telconet.ec</u></td>
					<td>07-2585848</td>
					<td>5801</td>
					<td>Mario Quirola</td>
                        	    </tr>
                                    <tr>
                            	        <td>QUEVEDO</td>
					<td><u style="color: blue;">ivillafuerte@telconet.ec</u></td>
					<td>05-2762652</td>
					<td>4005</td>
					<td>Ines Villafuerte</td>
                        	    </tr>
                                    <tr>
                            	        <td>SALINAS</td>
					<td><u style="color: blue;">ebalon@telconet.ec</u></td>
					<td>04-2779528</td>
					<td>2001</td>
					<td>Edilia Balon</td>
                        	    </tr>
				</table>
                            </td>
                        </tr>
                        
                    </table>
                </td>
            </tr>') ||
            TO_CLOB('
            <tr>
                <td>
                    &nbsp;
                </td>
            </tr>


	   <tr>
            	<td colspan="2">
        		<p><font size="2" face="Tahoma">Agradecemos su pronto pago evitando as&iacute; las molestias ocasionadas
por la suspensi&oacute;n.</p>
            	</td>
            </tr>
	   <tr>
            	<td colspan="2">
        		<p><strong><font size="2" face="Tahoma">NOTA IMPORTANTE: "Favor no considerar el presente
recordatorio si al momento de recibirlo Ud. ya efectu&oacute; su
respectivo pago".</strong></p>
            	</td>
            </tr>
            <tr>
            	<td colspan="2">
                	<p><strong><font size="2" face="Tahoma">Atentamente,</font></strong></p>
            	</td>
            </tr>
        	<tr>
            	<td colspan="2">
                	<p><strong><font size="2" face="Tahoma">Departamento de Cobranzas,<br>Telconet S.A.</font></strong></p>
            	</td>
            </tr> 
        </table>
    </body>
</html>') 
WHERE CODIGO = 'AVISOPAGO_TN';

UPDATE DB_GENERAL.ADMI_PARAMETRO_DET 
SET ESTADO = 'Eliminado'
WHERE PARAMETRO_ID =  
(SELECT ID_PARAMETRO
FROM DB_GENERAL.ADMI_PARAMETRO_CAB
WHERE NOMBRE_PARAMETRO = 'VARIABLES_NOTIFICACION_ENVIO_MASIVO'
AND ESTADO = 'Activo')
AND VALOR1='SALDO_CLIENTE'; 

COMMIT;