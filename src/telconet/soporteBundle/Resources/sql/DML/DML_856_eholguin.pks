SET DEFINE OFF;

DECLARE

BEGIN
--PLANTILLA CON CODIGO TAREA
UPDATE DB_COMUNICACION.ADMI_PLANTILLA SET PLANTILLA = TO_CLOB('<html>
<head>
<meta http-equiv=Content-Type content="text/html; charset=UTF-8">
</head>
<body>
<table align="center" width="40%" cellspacing="0" cellpadding="5">
<tr>
<td align="center">
<img alt="" height="250" width="600"  src="https://gallery.mailchimp.com/ecceab7377f33e3b122ec2a74/images/26d2b77d-827c-43de-b27b-f9f3e9e5bb75.png"/>
</td>
</tr>
<tr>
<td>
<table width="100%" cellspacing="0" cellpadding="5" align="center" style="font-family:arial;text-align: justify;">
<tr>
<td colspan="2" align = "center" ><h2>Hola {{nombrecliente}}</h2><hr></td>
</tr>

<tr>
<td colspan="2">
Felicitaciones, has escogido el plan: {{nombrePlan}} . Ya hemos registrado el contrato de tu servicio de Ultra Alta Velocidad en nuestro sistema y adicionalmente te lo adjuntamos en este correo para que puedas tenerlo siempre.
</td>
</tr>

<tr>
<td colspan="2">
Tu servicio ser&aacute; facturado en el {{nombreCicloFacturacion}}.
</td>
</tr>

<tr>
<td colspan="2">
Lo siguiente que haremos ser&aacute; enviar la petici&oacute;n a nuestra &aacute;rea de planificaci&oacute;n y log&iacute;stica, ellos revisar&aacute;n informaci&oacute;n t&eacute;cnica y asignar&aacute;n los recursos necesarios para tu servicio. Posteriormente se contactar&aacute;n contigo para agendar la visita de instalaci&oacute;n en una fecha y rango horario. Recuerda que el tiempo de instalaci&oacute;n promedio suele ser de 7 d&iacute;as h&aacute;biles, sin embargo podr&iacute;a ser superior en funci&oacute;n de las condiciones t&eacute;cnicas y de cobertura. Mientras tanto, quisi&eacute;ramos recomendarte los siguientes temas que seguro ser&aacute;n de utilidad:</td>
</tr>

<tr>
<td colspan="2" style="font-size:15">
<b>CARACTER&Iacute;STICAS DEL SERVICIO:</b>
</td>
</tr>
')||to_clob('
<tr>
<td colspan="2">
<ol>
<li>
El servicio contratado se encuentra en bits por segundo. El est&aacute;ndar para medir velocidad de enlaces es en bits por segundo y el de descarga de archivos es en bytes por segundo. (1 byte es igual a 8 bits)
</li>
<li>
El servicio no est&aacute; disponible para CYBERS o para cualquier actividad NO relacionada con la actividad inicialmente contratada, as&iacute; como tambi&eacute;n la reventa del mismo.
</li>
<li>
El equipo WiFi est&aacute;ndar tiene una cobertura horizontal que depende de los obst&aacute;culos que existan (paredes, puertas, pisos), por lo cual no se puede garantizar distancias con obst&aacute;culos ya que depende de la estructura/arquitectura de la residencia del cliente. Su cobertura horizontal CON obst&aacute;culos es de 10mts y SIN obst&aacute;culos es de 50mts de radio.
</li>
<li>
El tiempo de instalaci&oacute;n promedio del servicio es de 7 d&iacute;as h&aacute;biles, luego de haber firmado el contrato, haber entregado toda la documentaci&oacute;n y haber ingresado el contrato a nuestro sistema de gesti&oacute;n, sin embargo puede variar en funci&oacute;n de las condiciones t&eacute;cnicas y cobertura. No incluye obras civiles o cambios de acometida, la instalaci&oacute;n incluye 250m de fibra &oacute;ptica, el valor del metro de fibra &oacute;ptica adicional es de 1.00 USD + IVA.
</li>
<li>
NETLIFE ofrece soluciones de fibra &oacute;ptica invisible para residencias por un valor adicional.
</li>
<li>
Los equipos entregados son propiedad de MEGADATOS y deber&aacute;n ser devueltos en las oficinas de MEGADATOS al finalizar el contrato, caso contrario los equipos ser&aacute;n facturados y cobrados al cliente.
</li>
<li>
El cliente acepta que para salvaguardar la integridad de la red y evitar el SPAM el puerto 25 está bloqueado en servicios HOME.
</li>
<li>
El cliente conoce y acepta que la velocidad del plan contratado puede entregarse mediante conexi&oacute;n al&aacute;mbrica al equipo WiFi estandar ofrecido por NETLIFE. En el caso de realizar la conexi&oacute;n en forma inal&aacute;mbrica por WiFi, la tasa de transferencia llega hasta 30Mbps considerando las condiciones del punto 3. NETLIFE ofrece por un valor adicional SMARTWIFI que permite alcanzar velocidades superiores para planes de Ultra Alta Velocidad.
</li>

<li>
El cliente conoce y acepta las velocidades indicadas del plan suscrito en la solicitud de prestaci&oacute;n de servicios, incluyendo su compartici&oacute;n y velocidades m&iacute;nimas.
</li>

</ol>

</td>
</tr>
')||to_clob('

<tr>
<td colspan="2" style="font-size:15">
<b>PLAZO, FACTURACION Y FORMA DE PAGO:</b>
</td>
</tr>


<tr>
<td colspan="2">
<ol>
<li>
La duraci&oacute;n del contrato es de 36 meses, sin embargo el cliente acepta la cl&aacute;usula de permanencia m&iacute;nima por promociones de 24 meses.
</li>

<li>
Si el cliente termina anticipadamente el contrato, antes del tiempo de permanencia m&iacute;nima, deber&aacute; cancelar el valor total de las promociones con las que se benefici&oacute;.
</li>

<li>
Los valores a pagar est&aacute;n descritos en la solicitud de servicios y recibi&oacute; el contrato con dichos valores en forma legible.
</li>

<li>
El servicio contratado se paga durante los 5 primeros d&iacute;as de cada mes luego de recibir la FACTURA.
</li>

<li>
En caso de no haber pagado el servicio en el plazo establecido, el servicio podr&aacute; suspendido en cualquier momento.
</li>

<li>
En el caso de Planes con promociones por pago anticipado, debe ser pagado hasta m&aacute;ximo 1 semana luego de la fecha de activaci&oacute;n del plan.
</li>

<li>
Existen dos ciclos de facturaci&oacute;n. Si eres cliente del CICLO I el primer d&iacute;a del mes recibir&aacute;s tu factura electr&oacute;nica por el servicio mensual que comprende del 1 al 30/31 del mes pero si eres cliente del CICLO II el 15 del mes recibir&aacute;s tu factura electr&oacute;nica por el servicio mensual que comprende del 15 del mes corriente al 14 del siguiente mes.
</li>


</ol>

</td>
</tr>
')||to_clob('
<tr>
<td colspan="2" style="font-size:15">
<b>MEDIOS DE COMUNICACI&Oacute;N Y SOPORTE:</b>
</td>
</tr>

<tr>
<td colspan="2">
<ol>
<li>
Usted puede comunicarse con nuestro centro de atención al cliente 1-700-638-543, 37-31-300 o al correo electr&oacute;nico <a href="mailto:info@netlife.net.ec" target="_top"> info@netlife.net.ec</a> las 24 horas del d&iacute;a, los 365 d&iacute;as del a&ntilde;o.
</li>

<li>
En caso de soporte, el tiempo de atenci&oacute;n empieza desde el registro de la incidencia en nuestro centro de atenci&oacute;n, donde se entregar&aacute; un n&uacute;mero de ticket con el cual se podr&aacute; hacer seguimiento al caso.
</li>
</ol>
</td>
</tr>
<tr>
<td colspan="2">
Adicionalmente, hay algunos complementos que pueden ayudarte a mantener la mejor velocidad, seguridad y experiencia en Internet. Aqu&iacute; encontrar&aacute;s algunos links a temas que podr&iacute;an interesarte.
</td>
</tr>


<tr>
<td colspan="2">
<ul>
<li>
¿Interesado en proteger lo que m&aacute;s quieres en Internet? Visita <a href="http://bit.ly/1zdjkhl"  target="_blank">LINK </a>
</li>

<li>
¿Te gustar&iacute;a saber lo que sucede en tu hogar aunque no est&eacute;s presente? Visita <a href="http://bit.ly/12fkwSY"  target="_blank">LINK </a>
</li>

<li>
¿Te gustar&iacute;a tener 1.000GB de almacenamiento en la nube? Visita <a href="http://bit.ly/1zdjkhl"  target="_blank">LINK </a>
</li>

</ul>

</td>
</tr>
')||to_clob('
<tr>
<td colspan="2">
Finalmente, te sugerimos revisar nuestras gu&iacute;as de uso que contienen algunos consejos y consideraciones para que vivas la mejor experiencia con tu servicio e ingreses a nuestra secci&oacute;n de comunidad donde encontrar&aacute;s links a herramientas y contenido relevante:
</td>
</tr>


<tr>
<td colspan="2">
<ul>
<li>
<a href="http://www.netlife.ec/comunidad/" target="_blank">Secci&oacute;n Comunidad</a>
</li>

<li>
<a href="http://www.netlife.ec/comunidad/guias-de-usuario/" target="_blank">Gu&iacute;as de Usuario</a>

</li>

</ul>

</td>
</tr>


<tr>
<td colspan="2">
Si tienes cualquier requerimiento, puedes contactarnos a: <a href="mailto:soporte@netlife.net.ec" target="_top"> soporte@netlife.net.ec</a> o al 37-31-300
</td>
</tr>

<tr>
<td colspan="2" height="30"></td>
</tr>


<tr>
<td colspan="2">
EQUIPO NETLIFE <br>
1-700-683-543 | 37-31-300
</td>
</tr>


<tr>
<td colspan="2" height="30"></td>
</tr>

<tr>
<td colspan="2">

<table width="100%" cellspacing="0" cellpadding="5" bgcolor="#000000">
<tr>
<td width="140">
</td>
<td width="20">
<a href="https://www.facebook.com/netlife.ecuador?ref=ts&fref=ts" target="_blank">
<img border=0 width=24 height=24  src="http://cdn-images.mailchimp.com/icons/social-block-v2/color-facebook-48.png">
</a>
</td>
<td style="padding:0cm 0cm 0cm 3.75pt">
<a href="https://www.facebook.com/netlife.ecuador?ref=ts&fref=ts" target="_blank">/NetlifeEcuador</a>
<td>
</tr>

<tr>
<td>
</td>
<td>
<a href="https://twitter.com/NetlifeEcuador" target="_blank">
<img border=0 width=24 height=24  src="http://cdn-images.mailchimp.com/icons/social-block-v2/color-twitter-48.png">
</a>
</td>
<td style="padding:0cm 0cm 0cm 3.75pt">
<a href="https://twitter.com/NetlifeEcuador" target="_blank">@NetlifeEcuador</a>
<td>
</tr>
')||to_clob('
<tr>
<td>
</td>
<td>
<a href="https://www.youtube.com/user/NetlifeEcuador" target="_blank">
<img border=0 width=24 height=24 src="http://cdn-images.mailchimp.com/icons/social-block-v2/color-youtube-48.png">
</a>
</td>
<td style="padding:0cm 0cm 0cm 3.75pt">
<a href="https://www.youtube.com/user/NetlifeEcuador" target="_blank">/NetlifeEcuador</a>
<td>
</tr>


</table>

</td>
</tr>


</table>
</body>
</html>') WHERE CODIGO = 'CONTDIGITAL_NEW';

COMMIT;

EXCEPTION 
WHEN OTHERS THEN
ROLLBACK;

END;