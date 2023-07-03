/**
 * Documentación INSERT DE TÉRMINOS Y CONDICIONES DE PRODUCTOS MD
 *
 * @author Walther Joao Gaibor C <wgaibor@telconet.ec>
 * @version 1.0 24-05-2021
 */

SET SERVEROUTPUT ON 200000;
SET DEFINE OFF;

--=================================================================
declare
    bada clob:=' ';
begin

DBMS_LOB.APPEND(bada, '<p style="text-align: center;"><strong>T&Eacute;RMINOS Y CONDICIONES DE </strong><strong>SMART WIFI</strong></p>
<p> &nbsp;El Smart WIFI es un producto que sirve como Access Point (AP) y no como&nbsp;router.&nbsp;</p>
<ul>
<li>Este equipo se controla a trav&eacute;s de un controlador centralizado que permitir&aacute; optimizar la potencia y cobertura de varios Access&nbsp;Points&nbsp;en el mismo hogar para que no se interfieran entre ellos. Estos equipos son complementarios al&nbsp;router&nbsp;que se coloca normalmente.&nbsp;</li>
<li>En caso de renta del equipo este tiene un precio mensual de $9,99 (NUEVE D&Oacute;LARES DE LOS ESTADOS UNIDOS DE AM&Eacute;RICA CON 99/100) m&aacute;s IVA mensual. El cliente deber&aacute; firmar obligatoriamente un pagar&eacute; adicional.&nbsp;</li>
<li>En caso de que un cliente cancele el servicio de Netlife y haya contratado en servicio de Smart&nbsp;WiFi, debe entregar el equipo Smart Wifi con su respectivo cargador o fuente, de no hacerlo, la fuente tiene un costo de $90,00(NOVENTA D&Oacute;LARES DE LOS ESTADOS UNIDOS DE AM&Eacute;RICA)&nbsp;</li>
<li>​Para la instalaci&oacute;n del equipo Smart&nbsp;WiFi, Netlife incluye 1 metro de cable UTP para la conexi&oacute;n; y en caso de requerir m&aacute;s distancia de cable, el metro adicional de cable UTP es de $1,00(UN D&Oacute;LAR DE LOS ESTADOS UNIDOS DE AM&Eacute;RICA) m&aacute;s IVA&nbsp;</li>
<li>En caso de da&ntilde;o o p&eacute;rdida del equipo, el cliente deber&aacute; pagar el valor de $300,00(TRECIENTOS D&Oacute;LARES DE LOS ESTADOS UNIDOS DE AM&Eacute;RICA) m&aacute;s IVA.&nbsp;</li>
</ul>');
UPDATE DB_COMERCIAL.ADMI_PRODUCTO ADPR
SET ADPR.TERMINO_CONDICION = bada,
ADPR.FRECUENCIA = 'Mensual'
where ADPR.ID_PRODUCTO =201;

COMMIT;
END;

/

--=================================================================
declare
    bada clob:=' ';
begin

DBMS_LOB.APPEND(bada, '<p style="text-align: center;"><strong>T&Eacute;RMINOS Y CONDICIONES DE </strong><strong>IP FIJA </strong></p>
<ul>
<li>IP Fija es un producto que entrega una IP P&uacute;blica Fija en la WAN que act&uacute;an como un identificador &uacute;nico y permite disponer de una direcci&oacute;n exclusiva y reconocible en internet.&nbsp;</li>
<li>Las aplicaciones que precisan del uso de una IP&nbsp;fija&nbsp;son:&nbsp; Servidor de correo propio, Servidor para alojar una web o Intranet, Conexiones seguras en una Red Privada Virtual, entre otras.&nbsp;</li>
<li>Servicio s&oacute;lo disponible para planes PYME.&nbsp;&nbsp;&nbsp;</li>
<li>Este servicio se contrata como un adicional al plan de internet contratado por un precio de $10,00 (DIEZ D&Oacute;LARES DE LOS ESTADOS UNIDOS DE AM&Eacute;RICA) m&aacute;s IVA mensual&nbsp;(m&aacute;ximo 1 IP P&uacute;blica Fija en la WAN por punto del cliente).&nbsp;</li>
</ul>');
UPDATE DB_COMERCIAL.ADMI_PRODUCTO ADPR
SET ADPR.TERMINO_CONDICION = bada,
ADPR.FRECUENCIA = 'Mensual'
where ADPR.ID_PRODUCTO =66;

COMMIT;
END;

/
--=================================================================
declare
    bada clob:=' ';
begin

DBMS_LOB.APPEND(bada, '<p style="text-align: center;"><strong>T&Eacute;RMINOS Y CONDICIONES DE NETLIFECAM</strong></p>
<ul>
<li>NetlifeCam&nbsp;es una soluci&oacute;n que incluye&nbsp;el acondicionamiento de una c&aacute;mara de visualizaci&oacute;n remota&nbsp;para&nbsp;observar en tiempo real un lugar&nbsp;f&iacute;sico determinado.</li>
<li>El servicio incluye una&nbsp;c&aacute;mara&nbsp;Wi-Fi para interiores con resoluci&oacute;n HD, una tarjeta&nbsp;micro SD&nbsp;32 Gb&nbsp;m&aacute;s&nbsp;adaptador&nbsp;y&nbsp;la configuraci&oacute;n de la&nbsp;aplicaci&oacute;n de visualizaci&oacute;n remota.&nbsp;</li>
<li>El precio de&nbsp;NetlifeCam&nbsp;como servicio adicional es de $5.50(CINCO D&Oacute;LARES DE LOS ESTADOS UNIDOS DE AM&Eacute;RICA CON&nbsp;50/100) mensual m&aacute;s IVA.&nbsp;</li>
<li>El primer mes de servicio, el cliente no pagar&aacute; el valor total, sino el valor proporcional por el tiempo que haya recibido el servicio&nbsp;de acuerdo con&nbsp;su&nbsp;ciclo&nbsp;de facturaci&oacute;n&nbsp;(Ciclo 1: Del&nbsp;1 al 30&nbsp;del mes&nbsp;o&nbsp;Ciclo 2:&nbsp;Del 15 al 14&nbsp;del mes siguiente)&nbsp;</li>
<li>La instalaci&oacute;n del servicio incluye la visualizaci&oacute;n de la c&aacute;mara. No incluye cableado interno, configuraci&oacute;n de red local, ni trabajos de conexi&oacute;n el&eacute;ctrica.&nbsp;</li>
<li>El cliente entiende que la instalaci&oacute;n del servicio se realizar&aacute; m&aacute;ximo 10 metros del&nbsp;router&nbsp;WIfi&nbsp;para garantizar su calidad &oacute;ptima. Esta distancia podr&iacute;a ser menor y depender&aacute; de la cantidad de obst&aacute;culos e interferencias de se&ntilde;al que se detecten durante la instalaci&oacute;n.&nbsp;</li>
<li>NetlifeCam&nbsp;no incluye el servicio de grabaci&oacute;n en la nube.&nbsp;</li>
<li>La c&aacute;mara es para ambientes&nbsp;indoor&nbsp;no podr&aacute; ser utilizada para ambientes exteriores.&nbsp;</li>
<li>Se podr&aacute; activar el servicio si&nbsp;el cliente&nbsp;cuenta con una red inal&aacute;mbrica (WIFI).&nbsp;</li>
<li>Condiciones de funcionamiento: -10&ordm;C ~ 50&ordm;C, humedad 95% o menos (sin condensaci&oacute;n)&nbsp;</li>
<li>En el caso de da&ntilde;o por negligencia&nbsp;de la c&aacute;mara que se instala para este servicio, el cliente&nbsp;acepta&nbsp;el&nbsp;cobro del&nbsp;valor total de su reposici&oacute;n&nbsp;$60.00&nbsp;(SESENTA D&Oacute;LARES DE LOS ESTADOS UNIDOS DE AM&Eacute;RICA CON 00/100), valor que ser&aacute; gravado en la facturaci&oacute;n.&nbsp;</li>
<li>El cliente es absolutamente responsable de la informaci&oacute;n o contenido del servicio contratado, as&iacute; como de la transmisi&oacute;n de &eacute;sta a los clientes de Internet.&nbsp;</li>
<li>El cliente est&aacute; consciente que es el &uacute;nico responsable de grabar y respaldar la informaci&oacute;n de su propiedad que pueda derivarse de la visualizaci&oacute;n remota.&nbsp;</li>
<li>El cliente libera y mantiene a salvo a MEGADATOS de los da&ntilde;os y perjuicios que se ocasionen por accesos no autorizados, robo, da&ntilde;o, destrucci&oacute;n o desviaci&oacute;n de la informaci&oacute;n, archivos o programas que se relacionen de manera directa o indirecta con el &ldquo;Servicio&rdquo; prestado por MEGADATOS.&nbsp;</li>
<li>El cliente libera y mantiene a salvo a MEGADATOS de cualquier reclamaci&oacute;n, demanda y/o acci&oacute;n legal que pudiera derivarse del uso que el cliente o terceras personas relacionadas hagan del servicio, que implique da&ntilde;o, alteraci&oacute;n y/o modificaci&oacute;n a la red, medios y/o infraestructura a trav&eacute;s de la cual se presta el servicio.&nbsp;</li>
<li>El servicio tiene un tiempo de permanencia m&iacute;nima de 24 meses. En caso de cancelaci&oacute;n anticipada aplica&nbsp;el&nbsp;pago de los descuentos a los que haya accedido&nbsp;el cliente&nbsp;por promociones, tales como&nbsp;instalaci&oacute;n, tarifas preferenciales, etc.&nbsp;</li>
<li>En caso de requerirlo, el cliente podr&aacute; recibir soporte&nbsp;del servicio&nbsp;contact&aacute;ndose&nbsp;al 392000.&nbsp;</li>
<li>El cliente puede realizar la cancelaci&oacute;n del servicio&nbsp;en&nbsp;las&nbsp;oficinas de&nbsp;MEGADATOS&nbsp;En caso de que el cliente no&nbsp;devolviera&nbsp;la c&aacute;mara con su fuente respectiva&nbsp;y&nbsp;la&nbsp;tarjeta&nbsp;micro&nbsp;SD&nbsp;m&aacute;s adaptador,&nbsp;o&nbsp;se detecte mal uso o da&ntilde;os, el costo por reposici&oacute;n ser&aacute; facturado al cliente. En caso de&nbsp;la c&aacute;mara&nbsp;es de $60,00 (m&aacute;s IVA) y de&nbsp;la tarjeta&nbsp;micro SD&nbsp;m&aacute;s adaptador&nbsp;es de $20.00 (m&aacute;s IVA).&nbsp;</li>
<li>Limitaci&oacute;n de&nbsp;responsabilidad.-&nbsp;La Aplicaci&oacute;n es descargada y utilizada por el CLIENTE de forma libre y voluntaria, por lo que renuncia a reclamar a NETLIFE cualquier tipo de indemnizaci&oacute;n por el mal uso o funcionamiento de &eacute;sta. Se deja expresa constancia que para el correcto funcionamiento del producto NETLIFE CAM se deben reunir ciertos requisitos t&eacute;cnicos. Bajo ninguna circunstancia NETLIFE ser&aacute; responsable de las p&eacute;rdidas, da&ntilde;os y/o perjuicios que puedan presuntamente derivarse de la utilizaci&oacute;n del dispositivo y del contenido, puesto que la responsabilidad limita a la provisi&oacute;n del dispositivo(s), sin embargo el CLIENTE reconoce que el uso que d&eacute; al dispositivo se enmarcar&aacute; a las normas de la sana cr&iacute;tica, las buenas costumbres y las leyes del Ecuador, por ello su mal uso ser&aacute; derivado y entendido exclusivamente como responsabilidad del CLIENTE. Los Clientes deben utilizar este producto por su cuenta y riesgo. En ning&uacute;n caso NETLIFE ser&aacute; responsable por da&ntilde;os y/o perjuicios aun cuando &eacute;stos pudieran haber sido advertidos, as&iacute; como no ser&aacute; responsable de ning&uacute;n da&ntilde;o o p&eacute;rdida que pueda derivarse o relacionarse con el uso o falla del dispositivo, incluso en los casos derivados de uso inapropiado, impropio o fraudulento. La utilizaci&oacute;n de NETLIFE CAM por el Cliente implica la aceptaci&oacute;n por este &uacute;ltimo de la obligaci&oacute;n de indemnizar a NETLIFE o su personal por cualquier acci&oacute;n, reclamo da&ntilde;o, p&eacute;rdida y/o gasto, incluidas costas, honorarios de abogados, que se deriven de dicha utilizaci&oacute;n. Adicionalmente el Cliente entiende y acepta que este producto NETLIFE CAM es una herramienta t&eacute;cnica con cierto margen de tolerancia y no ofrece un resultado libre de error al 100% por lo tanto no constituye una prueba v&aacute;lida para reclamos posteriores. Reconoce adem&aacute;s que el dispositivo video grabar&aacute; el entorno en donde sea instalada, por cuenta del Cliente.&nbsp;</li>
</ul>');
UPDATE DB_COMERCIAL.ADMI_PRODUCTO ADPR
SET ADPR.TERMINO_CONDICION = bada,
ADPR.FRECUENCIA = 'Mensual'
where ADPR.ID_PRODUCTO =78;

COMMIT;
END;

/
--=================================================================
declare
    bada clob:=' ';
begin

DBMS_LOB.APPEND(bada, '<p>T&Eacute;RMINOS Y CONDICIONES DE IP FIJA ADICIONAL PYME</p>
<p>IP Fija Adicional PYME es un producto que entrega IPs P&uacute;blicas para LAN. Cada direcci&oacute;n IP es un enlace que se conecta a una interfaz WAN espec&iacute;fica. Servicio s&oacute;lo disponible para planes PYME. Este servicio se contrata como un adicional al plan de internet contratado por un precio por IP de $1,50 (UN D&Oacute;LAR DE LOS ESTADOS UNIDOS DE AM&Eacute;RICA CON 50/100) m&aacute;s IVA mensual (m&aacute;ximo 4 IPs P&uacute;blica Fija para LAN por punto del cliente).</p>');
UPDATE DB_COMERCIAL.ADMI_PRODUCTO ADPR
SET ADPR.TERMINO_CONDICION = bada,
ADPR.FRECUENCIA = 'Mensual'
where ADPR.ID_PRODUCTO =80;

COMMIT;
END;

/
--=================================================================
declare
    bada clob:=' ';
begin

DBMS_LOB.APPEND(bada, '<p><strong>T&Eacute;RMINOS Y CONDICIONES DE NETLIFE DEFENSE</strong>&nbsp;</p>
<p> &nbsp;</p>
<p>Netlife Defense es un servicio de seguridad inform&aacute;tica y control parental con 3(TRES) licencias multidispositivo provistas por Kaspersky, que permite reducir los riesgos de vulnerabilidades en la navegaci&oacute;n y transacciones por internet.&nbsp;</p>
<p><strong>Entre sus beneficios se incluye:</strong>&nbsp;&nbsp;</p>
<ul>
<li>Safe&nbsp;Kids&nbsp;(soluci&oacute;n de control parental)&nbsp;</li>
<li>Conexi&oacute;n segura.&nbsp;</li>
<li>Restricci&oacute;n de acceso no autorizado a la C&aacute;mara Web.&nbsp;</li>
<li>Safe&nbsp;Money (protecci&oacute;n de transacciones en l&iacute;nea).&nbsp;</li>
<li>Navegaci&oacute;n privada.&nbsp;</li>
<li>Antivirus,&nbsp;Antiransomware,&nbsp;Antibanner, Antispam.&nbsp;</li>
<li>Actualizador de software y PC&nbsp;Cleaner, entre otros.&nbsp;</li>
</ul>
<p>&nbsp;</p>
<p><strong>El m&eacute;todo de entrega de este servicio es mediante el env&iacute;o por correo electr&oacute;nico del c&oacute;digo de activaci&oacute;n desde&nbsp;</strong><a href="mailto:defense@netlife.ec"><strong>defense@netlife.ec</strong></a><strong>&nbsp;al correo registrado por el cliente en su contrato.</strong>&nbsp;</p>
<ul>
<li>Este correo debe ser un correo electr&oacute;nico v&aacute;lido. Es responsabilidad del cliente verificar que el correo no se encuentre alojado en la carpeta de correo no deseado. En caso de requerirlo, el cliente podr&aacute; solicitar el reenvi&oacute; de este correo a trav&eacute;s de nuestra central telef&oacute;nica 39 20000.&nbsp;</li>
<li>El precio de Netlife Defense como servicio adicional es de $2.75(DOS D&Oacute;LARES DE LOS ESTADOS UNIDOS DE AM&Eacute;RICA CON 75/100) mensual m&aacute;s IVA. Este&nbsp;servicio incluye 3(TRES) licencias multidispositivo. Netlife Defense est&aacute; disponible &uacute;nicamente con el servicio de Internet de Netlife.&nbsp;</li>
<li>El primer mes de servicio, el cliente no pagar&aacute; el valor total, sino el valor proporcional por el tiempo que haya recibido el servicio de acuerdo con su ciclo de facturaci&oacute;n (Ciclo 1: Del 1 al 30 del mes o Ciclo 2: Del 15 al 14 del mes siguiente).&nbsp;</li>
<li>Para que esta soluci&oacute;n de seguridad inform&aacute;tica est&eacute; en operaci&oacute;n, es necesaria la instalaci&oacute;n del software en el dispositivo que requiera protegerse. Es de exclusiva responsabilidad del cliente su efectiva instalaci&oacute;n. Conozca el proceso de instalaci&oacute;n por dispositivo aqu&iacute;.  <a href="https://www.netlife.ec/netlife-defense-repositorio/">https://www.netlife.ec/netlife-defense-repositorio/</a>.&nbsp;</li>
<li>Netlife Defense soporta: Equipos de escritorio y port&aacute;tiles: Windows 10/8.1 /8 /7 o superior; OS X 10.12 &ndash; macOS 10.13 o superiores;&nbsp;Tablets: Windows 10 / 8 &amp; 8.1 /&nbsp;Pro (64 bits); iOS 9.0 o posterior; Smartphones: Android 4.1 o posterior, iOS 9.0 o posterior.&nbsp;</li>
<li>Puede ser instalado en el n&uacute;mero de computadoras y dispositivos Android indicados en el paquete (3(TRES) dispositivos en cualquier combinaci&oacute;n). Siempre que no exceda el n&uacute;mero de dispositivos permitidos, podr&aacute; desinstalar y reinstalar el producto, adem&aacute;s de usar el c&oacute;digo de activaci&oacute;n cuando sea necesario.&nbsp;</li>
<li>En el caso de Android el producto est&aacute; disponible descargando la aplicaci&oacute;n Kaspersky Internet Security. En el caso de dispositivos iOS, el producto est&aacute; disponible; a trav&eacute;s, de la aplicaci&oacute;n Kaspersky&nbsp;Safe&nbsp;Browser (para navegaci&oacute;n segura).&nbsp;</li>
<li>La soluci&oacute;n de control parental est&aacute; disponible en todos los dispositivos. Para activarla es necesario instalar Kaspersky&nbsp;Safe&nbsp;Kids&nbsp;tanto en&nbsp;PCs&nbsp;como en dispositivos m&oacute;viles.&nbsp;</li>
<li>La protecci&oacute;n de la c&aacute;mara web est&aacute; habilitada para PC y Mac.&nbsp;</li>
<li>Todos los planes&nbsp;Home&nbsp;y Pyme&nbsp;(seg&uacute;n la oferta&nbsp;establecida por Netlife),&nbsp;incluyen Netlife Defense, un sistema de seguridad inform&aacute;tica y control parental con 3(TRES) licencias multidispositivo para protecci&oacute;n en internet provisto por Kaspersky. La protecci&oacute;n inicia al momento de instalar el software en el dispositivo, es de exclusiva responsabilidad del cliente su efectiva instalaci&oacute;n.&nbsp;</li>
<li>Para casos en los que ya se encuentre activa&nbsp;tu licencias&nbsp;en uno de tus dispositivos y requieras reutilizarla en otro dispositivo, debes seguir los siguientes pasos:&nbsp;</li>
</ul>
<p><strong>1.</strong> Desinstalar tu licencia activa del dispositivo&nbsp;<br /><strong>2.</strong> Eliminar el dispositivo de tu administrador en <a href="https://my.kaspersky.com/%22%20/t%20%22_blank">My&nbsp;kaspersky&nbsp;<br /></a><strong>3.</strong> Esperar 72 horas m&iacute;nimo antes de volver a instalar la licencia en otro dispositivo para evitar que la licencia se bloquee.&nbsp;</p>
<ol start="4">
<li>Si presentas inconvenientes en el primer intento de la instalaci&oacute;n de tu licencia Netlife Defense cont&aacute;ctate con nosotros al 3920000 para poder asesorarte en el proceso.&nbsp;</li>
</ol>
<p>&nbsp;</p>
<p><strong>Requisitos de operaci&oacute;n:</strong>&nbsp;</p>
<p><strong>Requerimientos m&iacute;nimos del sistema para la instalaci&oacute;n de Netlife Defense:&nbsp;</strong>&nbsp;</p>
<ul>
<li>Disco&nbsp;Duro: Windows: 1.500 MB; Mac 1220 MB. Memoria (RAM) libre:1 GB (32 bits) o 2 GB (64 bits).</li>
<li>Resoluci&oacute;n m&iacute;nima de pantalla 1024&times;600 (para&nbsp;tablets&nbsp;con Windows), 320&times;480 (para dispositivos Android).&nbsp;</li>
<li>Conexi&oacute;n Activa a Internet.&nbsp;</li>
</ul>');
UPDATE DB_COMERCIAL.ADMI_PRODUCTO ADPR
SET ADPR.TERMINO_CONDICION = bada,
ADPR.FRECUENCIA = 'Mensual'
where ADPR.ID_PRODUCTO =210;

COMMIT;
END;

/
--=================================================================
declare
    bada clob:=' ';
begin

DBMS_LOB.APPEND(bada, '<p style="text-align: center;"><strong>T&Eacute;RMINOS Y CONDICIONES DE </strong><strong>SMART WIFI</strong></p>
<p> &nbsp;El Smart WIFI es un producto que sirve como Access Point (AP) y no como&nbsp;router.&nbsp;</p>
<ul>
<li>Este equipo se controla a trav&eacute;s de un controlador centralizado que permitir&aacute; optimizar la potencia y cobertura de varios Access&nbsp;Points&nbsp;en el mismo hogar para que no se interfieran entre ellos. Estos equipos son complementarios al&nbsp;router&nbsp;que se coloca normalmente.&nbsp;</li>
<li>En caso de renta del equipo este tiene un precio mensual de $9,99 (NUEVE D&Oacute;LARES DE LOS ESTADOS UNIDOS DE AM&Eacute;RICA CON 99/100) m&aacute;s IVA mensual. El cliente deber&aacute; firmar obligatoriamente un pagar&eacute; adicional.&nbsp;</li>
<li>En caso de que un cliente cancele el servicio de Netlife y haya contratado en servicio de Smart&nbsp;WiFi, debe entregar el equipo Smart Wifi con su respectivo cargador o fuente, de no hacerlo, la fuente tiene un costo de $90,00(NOVENTA D&Oacute;LARES DE LOS ESTADOS UNIDOS DE AM&Eacute;RICA)&nbsp;</li>
<li>​Para la instalaci&oacute;n del equipo Smart&nbsp;WiFi, Netlife incluye 1 metro de cable UTP para la conexi&oacute;n; y en caso de requerir m&aacute;s distancia de cable, el metro adicional de cable UTP es de $1,00(UN D&Oacute;LAR DE LOS ESTADOS UNIDOS DE AM&Eacute;RICA) m&aacute;s IVA&nbsp;</li>
<li>En caso de da&ntilde;o o p&eacute;rdida del equipo, el cliente deber&aacute; pagar el valor de $300,00(TRECIENTOS D&Oacute;LARES DE LOS ESTADOS UNIDOS DE AM&Eacute;RICA) m&aacute;s IVA.&nbsp;</li>
</ul>');
UPDATE DB_COMERCIAL.ADMI_PRODUCTO ADPR
SET ADPR.TERMINO_CONDICION = bada,
ADPR.FRECUENCIA = 'Mensual'
where ADPR.ID_PRODUCTO =864;

COMMIT;
END;

/
--=================================================================
declare
    bada clob:=' ';
begin

DBMS_LOB.APPEND(bada, '<p style="text-align: center;"><strong>T&Eacute;RMINOS Y CONDICIONES DE NETLIFE CLOUD</strong></p>
<p> Netlife Cloud es un servicio que incluye Microsoft 365 Familia con almacenamiento de 6.000GB dividido entre 6(SEIS) usuarios y 60(SESENTA) minutos de llamadas Skype mensuales.&nbsp;</p>
<ul>
<li>Para su uso debe activar las credenciales enviadas desde el correo&nbsp;<a href="mailto:notificacionesnetlife@netlife.info.ec">notificacionesnetlife@netlife.info.ec</a>al correo registrado por el cliente en su contrato, e instalar los aplicativos.&nbsp;</li>
<li>Para poder activar este producto y administrar las suscripciones es requisito contar con una cuenta de Microsoft (@outlook.com, @hotmail.com, @hotmail.es, @live.com). Si no tiene este tipo de cuenta de correo, puedes crear una en la direcci&oacute;n:&nbsp;<a href="https://signup.live.com/">https://signup.live.com/</a></li>
<li>La entrega de este producto no incluye el servicio de instalaci&oacute;n&nbsp;del mismo&nbsp;en ning&uacute;n dispositivo. El cliente es responsable de la instalaci&oacute;n y configuraci&oacute;n del producto en sus dispositivos y usuarios.&nbsp;</li>
<li>Los pasos para instalar y empezar a utilizar Microsoft 365 Familia se encuentran en el siguiente&nbsp;link:&nbsp;<a href="https://office.com/setup">com/setup</a>. Para administrar los dispositivos y cuentas de su licencia Microsoft 365 Familia el cliente puede acceder al&nbsp;link:&nbsp;<a href="https://office.com/myaccount">office.com/myaccount</a></li>
<li>Netlife Cloud tiene un precio de $7,99(SIETE D&Oacute;LARES DE LOS ESTADOS&nbsp;UNIDOS DE AM&Eacute;RICA CON 99/100) m&aacute;s IVA&nbsp;mensual, que se a&ntilde;ade a planes de Internet de Netlife.&nbsp;</li>
<li>El primer mes de servicio, el cliente no pagar&aacute; el valor total, sino el valor proporcional por el tiempo que haya recibido el servicio de acuerdo con su ciclo de facturaci&oacute;n (Ciclo 1: Del 1 al 30 del mes o Ciclo 2: Del 15 al 14 del mes siguiente).&nbsp;</li>
<li>Netlife Cloud puede ser comercializado a personas naturales, profesionales o microempresarios sin RUC, no a PYMES.&nbsp;</li>
<li>El servicio tiene una vigencia de 12(DOCE) meses e incluye renovaci&oacute;n autom&aacute;tica de licencia. En caso de cancelarlo antes de los 12(DOCE) meses de cualquiera de sus per&iacute;odos de vigencia y renovaci&oacute;n, el cliente deber&aacute; pagar el valor proporcional, de acuerdo con el tiempo de vigencia que resta por cubrir.&nbsp;</li>
<li>El canal de soporte para consultas, dudas o requerimientos espec&iacute;ficos del producto Microsoft 365 Familia podr&aacute; ser realizado a trav&eacute;s del tel&eacute;fono: 1-800-010-288&nbsp;</li>
<li>Netlife Cloud se puede instalar en&nbsp;PCs&nbsp;y tabletas Windows que ejecuten Windows 7 o una versi&oacute;n posterior, y equipos Mac con Mac OS X 10.6 o una versi&oacute;n posterior.&nbsp;</li>
<li>Microsoft 365 Familia para iPad se puede instalar en iPads que ejecuten la &uacute;ltima versi&oacute;n de iOS. Microsoft 365 Mobile para iPhone se puede instalar en tel&eacute;fonos que ejecuten iOS 6.0 o una versi&oacute;n posterior.&nbsp;</li>
<li>Microsoft 365 Mobile para tel&eacute;fonos Android se puede instalar en tel&eacute;fonos que ejecuten OS 4.0 o una versi&oacute;n posterior. Para obtener m&aacute;s informaci&oacute;n sobre los dispositivos y requerimientos, visite:&nbsp;<a href="http://www.office.com/information">office.com/information</a>.&nbsp;</li>
</ul>
<p><strong>Requisitos de operaci&oacute;n:</strong>&nbsp;</p>
<ul>
<li>El cliente es responsable de mantener una energ&iacute;a el&eacute;ctrica regulada de 110V y de contar con dispositivos compatibles a las condiciones m&iacute;nimas de operaci&oacute;n del producto.&nbsp;</li>
<li>Procesador y memoria RAM: procesador x86/x64 de 1 GHz o superior con conjunto de instrucciones SSE2 (PC), procesador Intel (Mac), Memoria: 1 GB de RAM (32 bits o Mac), 2 GB de RAM (64 bits)&nbsp;</li>
<li>Disco duro: 3 GB de espacio disponible en disco (PC), 2.5 GB de HFS+ formato de disco duro (Mac), Sistema operativo (PC): Windows 7 o superior de 32 bits o 64 bits; Windows 2008 R2 o superior con .NET 3.5 o superior. El nuevo Office no se puede instalar en un PC con Windows XP o Vista. Para usar con Windows 8, se debe contar con la versi&oacute;n&nbsp;Release&nbsp;Preview&nbsp;o superior, Sistema operativo (Mac): Mac OS X versi&oacute;n 10.5.8 o superior.&nbsp;</li>
<li>Gr&aacute;ficos: La aceleraci&oacute;n gr&aacute;fica de hardware requiere una tarjeta gr&aacute;fica DirectX 10 y resoluci&oacute;n de 1366 x 728 (PC); 1280 x 800 de resoluci&oacute;n de pantalla (Mac).&nbsp;</li>
</ul>');
UPDATE DB_COMERCIAL.ADMI_PRODUCTO ADPR
SET ADPR.TERMINO_CONDICION = bada,
ADPR.FRECUENCIA = 'Mensual'
where ADPR.ID_PRODUCTO =939;

COMMIT;
END;

/
--=================================================================
declare
    bada clob:=' ';
begin

DBMS_LOB.APPEND(bada, '<p style="text-align: center;"><strong>T&Eacute;RMINOS Y CONDICIONES DE NETLIFE </strong><strong>ASSISTANCE</strong></p>
<p style="text-align: center;"> &nbsp;<strong>Netlife Assistance</strong></p>
<p>Netlife&nbsp;Assistance&nbsp;es un servicio que brinda soluciones remotas ilimitadas de asistencia t&eacute;cnica para equipos terminales del cliente, entre los cuales est&aacute;n:&nbsp;</p>
<ul>
<li>Asistencia guiada de configuraci&oacute;n e instalaci&oacute;n de software o hardware.&nbsp;</li>
<li>Revisi&oacute;n, an&aacute;lisis y mantenimiento del PC/MAC.&nbsp;</li>
<li>Asesor&iacute;a t&eacute;cnica en l&iacute;nea las 24 horas del PC/MAC.&nbsp;</li>
<li>T&eacute;cnico PC y dispositivos remoto ilimitado.&nbsp;</li>
<li>Incluye hasta 3 visitas presenciales al a&ntilde;o.&nbsp;</li>
<li>Para acceder al servicio y recibir asistencia y soporte de un t&eacute;cnico especialista es necesario contactarse por v&iacute;a telef&oacute;nica al 39 20000.&nbsp;</li>
</ul>
<p><strong>Modalidad mensualizada</strong>&nbsp;</p>
<ul>
<li><strong>Netlife&nbsp;Assistance&nbsp;como servicio adicional</strong>mensualizado tiene un precio promocional de $1,99(UN D&Oacute;LAR DE LOS ESTADOS UNIDOS DE AM&Eacute;RICA CON 99/100) m&aacute;s IVA&nbsp;mensual, que se a&ntilde;ade&nbsp;a planes de Internet HOME de Netlife.&nbsp;</li>
<li>El servicio tiene un tiempo de permanencia m&iacute;nima de 12(DOCE) meses. En caso de que un cliente no permanezca los 12(DOCE) meses, entonces se le cobrar&aacute; el valor de las promociones. Valor normal del servicio: $8.75(OCHO D&Oacute;LARES DE LOS&nbsp;ESTADOS UNIDOS DE AM&Eacute;RICA CON 75/100)+iva&nbsp;mensual.&nbsp;</li>
<li>El primer mes de servicio, el cliente no pagar&aacute; el valor total, sino el valor proporcional por el tiempo que haya recibido el servicio de acuerdo con su ciclo de facturaci&oacute;n (Ciclo 1: Del 1 al 30 del mes o Ciclo 2: Del 15 al 14 del mes siguiente).&nbsp;</li>
<li>El servicio no incluye materiales, sin embargo, si el cliente los requiere se cobrar&aacute;n por separado. Tampoco incluye reparaci&oacute;n de equipos o dispositivos.&nbsp;</li>
<li>El servicio aplica para planes HOME en las ciudades de Quito y Guayaquil. Servicio no disponible para planes PYME.&nbsp;</li>
</ul>
<p><strong>Modalidad bajo demanda</strong>&nbsp;</p>
<ul>
<li>Precio del servicio: $30,00 (TREINTA D&Oacute;LARES DE LOS ESTADOS UNIDOS DE AM&Eacute;RICA CON) m&aacute;s IVA la visita en ciudad y $35(TREINTA Y CINCO D&Oacute;LARES DE LOS ESTADOS UNIDOS DE AM&Eacute;RICA) m&aacute;s IVA en zonas for&aacute;neas.&nbsp;</li>
<li>Duraci&oacute;n de la visita 1(UNA) hora.&nbsp;</li>
<li>Adicional a la primera hora de atenci&oacute;n se cobrar&aacute; 10,00(DIEZ D&Oacute;LARES DE LOS ESTADOS UNIDOS DE AM&Eacute;RICA) m&aacute;s IVA. Los costos no incluyen materiales.&nbsp;</li>
<li>El servicio aplica para planes HOME en las ciudades de Quito y Guayaquil.&nbsp;</li>
<li>Visitas presenciales para clientes de NETLIFE exclusivas para reparaci&oacute;n y/o revisi&oacute;n de problemas en redes internas ya establecidas.&nbsp;</li>
</ul>
<p><strong>Dichas visitas no incluyen trabajos como:</strong>&nbsp;</p>
<ul>
<li>Trabajos el&eacute;ctricos o trabajos en alturas externos&nbsp;</li>
<li>No pasar gu&iacute;as sobre&nbsp;ducter&iacute;a, ni cables por ductos sin gu&iacute;a&nbsp;</li>
<li>Obras civiles&nbsp;</li>
<li>Instalaci&oacute;n de Software no licenciado&nbsp;</li>
<li>No formatear PC&nbsp;</li>
</ul>
<p><strong>Ni Materiales Adicionales como:</strong>&nbsp;</p>
<ul>
<li>Canaletas Pl&aacute;sticas Adhesivas&nbsp;</li>
<li>Cable UTP 5e&nbsp;</li>
<li>Jack RJ45&nbsp;</li>
</ul>');
UPDATE DB_COMERCIAL.ADMI_PRODUCTO ADPR
SET ADPR.TERMINO_CONDICION = bada,
ADPR.FRECUENCIA = 'Mensual'
where ADPR.ID_PRODUCTO =1130;

COMMIT;
END;

/
--=================================================================
declare
    bada clob:=' ';
begin

DBMS_LOB.APPEND(bada, '<p style="text-align: center;"><strong>T&Eacute;RMINOS Y CONDICIONES DE </strong><strong>FIBRA INVISIBLE FTTR (FIBER TO THE ROOM)&nbsp;NETFIBER</strong>&nbsp;</p>
<p> El servicio contempla el cableado con fibra &oacute;ptica hasta un punto espec&iacute;fico dentro del hogar.&nbsp;</p>
<ul>
<li>El precio de servicio es de $125,00(CIENTO VEINTI CINCO D&Oacute;LARES DE LOS ESTADOS UNIDOS DE AM&Eacute;RICA) m&aacute;s IVA (&uacute;nico pago), e incluye 50(CINCUENTA)&nbsp;mts&nbsp;de fibra invisible, conversor &oacute;ptico el&eacute;ctrico y&nbsp;Swtich&nbsp;de 4 puertos Gbps e instalaci&oacute;n.&nbsp;</li>
<li>Disponible para Quito y Guayaquil. El metro adicional de fibra tiene un precio de $30,00(TREINTA D&Oacute;LARES DE LOS ESTADOS UNIDOS DE AM&Eacute;RICA) m&aacute;s IVA.&nbsp;</li>
</ul>
<p><strong>Restricciones:</strong>&nbsp;</p>
<ul>
<li>Factibilidad geogr&aacute;fica y t&eacute;cnica.&nbsp;</li>
<li>La velocidad ofertada depende de la capacidad y procesamiento que soporte el dispositivo final del&nbsp;cliente,&nbsp;as&iacute; como del&nbsp;routerwifi&nbsp;y la capacidad del sitio remoto de contenido.&nbsp;</li>
<li>Se recomienda conexi&oacute;n al&aacute;mbrica de los equipos para obtener la m&aacute;xima velocidad disponible con una eficiencia del 90% (NOVENTA POR CIENTO).&nbsp;</li>
<li>En caso de realizar la conexi&oacute;n mediante wifi a 2.4Ghz la velocidad m&aacute;xima que permite este tipo de tecnolog&iacute;a que es de 40Mbps y en la banda de 5GHz llega hasta 100Mbps a una distancia de 3(TRES) metros y sin obst&aacute;culos, en otras condiciones se tendr&aacute;n velocidades menores.&nbsp;</li>
</ul>');
UPDATE DB_COMERCIAL.ADMI_PRODUCTO ADPR
SET ADPR.TERMINO_CONDICION = bada,
ADPR.FRECUENCIA = 'Unica'
where ADPR.ID_PRODUCTO =1207;

COMMIT;
END;

/
--=================================================================
declare
    bada clob:=' ';
begin

DBMS_LOB.APPEND(bada, '<p style="text-align: center;"><strong>T&Eacute;RMINOS Y CONDICIONES DE EXTENSOR WIFI DUAL BAND</strong></p>
<ul>
<li>El&nbsp;equipo&nbsp;Extender Dual Band se encuentran disponible solo en ciudades con tecnolog&iacute;a Huawei&nbsp;</li>
<li>Los planes que deseen contratar un Extender Dual Band, pueden acceder a &eacute;l pagando $4,50(CUATRO D&Oacute;LARES DE LOS ESTADOS UNIDOS DE AM&Eacute;RICA CON 50/100) m&aacute;s impuestos mensuales. No hay costo de visita t&eacute;cnica. Se pueden incluir hasta 3 (TRES)&nbsp;Extenders&nbsp;por servicio. Aplica para ciudades con tecnolog&iacute;a Huawei.&nbsp;</li>
<li>El primer mes de servicio, el cliente no pagar&aacute; el valor total, sino el valor proporcional por el tiempo que haya recibido el servicio de acuerdo con su ciclo de facturaci&oacute;n (Ciclo 1: Del 1 al 30 del mes o Ciclo 2: Del 15 al 14 del mes siguiente).&nbsp;</li>
<li>Los equipos son propiedad de MEGADATOS S.A. y cuenta con una garant&iacute;a de 1(UN) a&ntilde;o por defectos de f&aacute;brica. Al finalizar la prestaci&oacute;n del servicio el cliente deber&aacute; entregarlos&nbsp;en las oficinas de MEGADATOS. En caso de que el cliente no lo devolviere, se detecte mal uso o da&ntilde;os, el costo total del equipo por reposici&oacute;n ser&aacute; facturado al cliente:&nbsp;80,00(OCHENTA D&Oacute;LARES DE LOS ESTADOS UNIDOS DE AM&Eacute;RICA) (m&aacute;s IVA) para el equipo WIFI Dual Band Standard, USD$ 175 (CIENTO SETENTA Y CINCO D&Oacute;LARES DE LOS ESTADOS UNIDOS DE AM&Eacute;RICA) (m&aacute;s IVA) para el&nbsp;ONT+WiFi&nbsp;Dual Band Premium y USD$ 75 (SETENTA Y CINCO D&Oacute;LARES DE LOS ESTADOS UNIDOS DE AM&Eacute;RICA) (m&aacute;s IVA) para el equipo AP Extender&nbsp;WiFi&nbsp;Dual Band.&nbsp;</li>
<li>El cliente conoce y acepta que, para garantizar la calidad&nbsp;del servicio, estos equipos ser&aacute;n administrado por NETLIFE mientras dure la prestaci&oacute;n del servicio.&nbsp;</li>
<li>El equipo&nbsp;WiFi&nbsp;provisto por NETIFE tiene puertos al&aacute;mbricos que permiten la utilizaci&oacute;n &oacute;ptima de la velocidad ofertada en el plan contratado, adem&aacute;s cuenta con conexi&oacute;n&nbsp;WiFi&nbsp;a una frecuencia de 5Ghz que permite usna velocidad m&aacute;xima de&nbsp;150Mbps a una distancia de 3 metros y pueden conectarse equipos a una distancia de hasta 12 metros en condiciones normales, sin embargo, la distancia de cobertura var&iacute;a seg&uacute;n la cantidad y tipo de paredes, obst&aacute;culos e interferencia que se encuentren en el entorno. El cliente conoce y acepta que la tecnolog&iacute;a&nbsp;WiFi&nbsp;pierde potencia a mayor distancia y por lo tanto se reducir&aacute; la velocidad efectiva a una mayor distancia de conexi&oacute;n del equipo.&nbsp;</li>
</ul>
<p style="text-align: center;"><strong>Promoci&oacute;n</strong></p>
<ul>
<li>25% (VEINTICINCO&nbsp;POR CIENTO) de descuento en las 3 primeras facturas del servicio del&nbsp;1&nbsp;al 31&nbsp;de&nbsp;agosto&nbsp;del 2021, aplica solo para clientes que agreguen este servicio como adicional a su plan de internet dentro de la fecha estipulada.&nbsp;</li>
<li>Precio despu&eacute;s de finalizar la promoci&oacute;n: $5.04&nbsp;(CINCO&nbsp;D&Oacute;LARES DE LOS ESTADOS UNIDOS DE AM&Eacute;RICA CON 04/100) mensual&nbsp;incluido impuestos.&nbsp;&nbsp;</li>
</ul>');
UPDATE DB_COMERCIAL.ADMI_PRODUCTO ADPR
SET ADPR.TERMINO_CONDICION = bada,
ADPR.FRECUENCIA = 'Mensual'
where ADPR.ID_PRODUCTO =1232;

COMMIT;
END;

/
--=================================================================
declare
    bada clob:=' ';
begin

DBMS_LOB.APPEND(bada, '<p style="text-align: center;"><strong>T&Eacute;RMINOS Y CONDICIONES NETLIFE ASSISTANCE PRO</strong></p>
<p style="text-align: center;"> &nbsp;<strong>Netlife Assistance PRO</strong></p>
<p>Netlife&nbsp;Assistance&nbsp;Pro&nbsp;es un servicio que brinda soluciones a los problemas t&eacute;cnicos e inform&aacute;ticos de un negocio para mejorar su operaci&oacute;n, disponible para 5 usuarios. Para acceder a &eacute;l es necesario ingresar dentro de la secci&oacute;n &ldquo;Netlife Access&rdquo; en la p&aacute;gina web de Netlife o a store.netlife.net.ec&nbsp;</p>
<p style="text-align: left;"><strong>Este servicio incluye:</strong></p>
<ul>
<li>Asistencia guiada de configuraci&oacute;n, sincronizaci&oacute;n y conexi&oacute;n a red de software o hardware: PC, MAC.&nbsp;</li>
<li>Revisi&oacute;n, an&aacute;lisis y mantenimiento del PC/MAC/LINUX/SmartTV/Smartphones/Tablets/Apple TV/Roku, etc.&nbsp;</li>
<li>Asesor&iacute;a t&eacute;cnica en l&iacute;nea las 24 horas v&iacute;a telef&oacute;nica o web por&nbsp;<a href="https://store.netlife.net.ec/">netlife.net.ec.&nbsp;</a></li>
<li>Un servicio de&nbsp;Help&nbsp;Desk&nbsp;con ingenieros especialistas.&nbsp;</li>
<li>Se puede ayudar a reinstalar el Sistema Operativo del dispositivo del cliente, siempre y cuando se disponga de las licencias y medios de instalaci&oacute;n originales correspondientes.&nbsp;</li>
</ul>
<p><strong>Sistemas Operativos sobre los cuales se brinda soporte a incidencias son:&nbsp;</strong>&nbsp;</p>
<ul>
<li>Windows: XP hasta 10, Windows Server: 2003 hasta 2019,&nbsp;MacOs: 10.6 (Snow&nbsp;Leopard) hasta 10.14 (Mojave), Linux: Ubuntu 19.04, Fedora 30, Open SUSE 15.1, Debian 10.0, Red&nbsp;Hat&nbsp;8, CentOS 7, iOS: 7.1.2 a 12.3.2, Android: Ice&nbsp;Cream&nbsp;Sandwich&nbsp;4.0 hasta Pie 9.0, Windows&nbsp;Phone&nbsp;OS: 8.0 hasta 10 Mobile&nbsp;</li>
</ul>
<p><strong>Asistencia Hardware:&nbsp;</strong>&nbsp;</p>
<ul>
<li>Los controladores o software necesarios para el funcionamiento del hardware son responsabilidad del usuario, sin embargo, se dar&aacute; apoyo para obtenerlos en caso de ser necesario.&nbsp;</li>
</ul>
<p><strong>Asistencia Software:</strong>&nbsp;</p>
<ul>
<li>No incluye capacitaci&oacute;n en el uso del Software. Las licencias y medios de instalaci&oacute;n son a cargo del usuario. Nunca se prestar&aacute; ayuda sobre software ilegal.&nbsp;</li>
<li>No incluye capacitaci&oacute;n en el uso del Sistema Operativo y software, &uacute;nicamente se solucionar&aacute;n incidencias puntuales.&nbsp;</li>
</ul>
<p><strong>Para recibir asistencia se dispone de 3(TRES) canales de atenci&oacute;n habilitados las 24(VEINTI CUATRO) horas del d&iacute;a:&nbsp;</strong>&nbsp;</p>
<ul>
<li>Chat, llamada telef&oacute;nica y correo electr&oacute;nico.&nbsp;</li>
</ul>
<p><strong>El tiempo de atenci&oacute;n de los distintos canales son:</strong>&nbsp;</p>
<ul>
<li>Chat 30(TREINTA) segundos, v&iacute;a telef&oacute;nica 60(SESENTA) segundos (3920000), y v&iacute;a correo electr&oacute;nico 20(VEINTE) minutos (<a href="mailto:soporte@store.netlife.net.ec">soporte@store.netlife.net.ec</a>)&nbsp;</li>
<li>Se mantendr&aacute; en la plataforma durante 60(SESENTA) d&iacute;as, el 100% de las conversaciones chat levantadas v&iacute;a web; a trav&eacute;s de,&nbsp;<a href="https://store.netlife.net.ec/">netlife.net.ec</a></li>
</ul>
<p><strong>Netlife&nbsp;Assistance&nbsp;Pro&nbsp;como servicio adicional</strong>&nbsp;</p>
<ul>
<li>Tiene un precio de $2,99(DOS D&Oacute;LARES DE LOS ESTADOS UNIDOS DE AM&Eacute;RICA CON 99/100) m&aacute;s&nbsp;IVA&nbsp;mensual, que&nbsp;se a&ntilde;ade a planes de Internet de&nbsp;Netlife.&nbsp;</li>
<li>El primer mes de servicio, el cliente no pagar&aacute; el valor total, sino el valor proporcional por el tiempo que haya recibido el servicio de acuerdo con su ciclo de facturaci&oacute;n (Ciclo 1: Del 1 al 30 del mes o Ciclo 2: Del 15 al 14 del mes siguiente).&nbsp;</li>
<li>El servicio tiene un tiempo de permanencia m&iacute;nima de 12(DOCE) meses.&nbsp;En caso de cancelaci&oacute;n anticipada aplica el pago de los descuentos a los que haya accedido el cliente por promociones, tales como instalaci&oacute;n, tarifas preferenciales, etc.&nbsp;</li>
<li>El servicio de Netlife&nbsp;Assistance&nbsp;Pro, no incluye visitas presenciales, pero si el cliente lo requiere podr&aacute; coordinar dichas visitas por un costo adicional de $30(TREINTA D&Oacute;LARES DE LOS ESTADOS UNIDOS DE AM&Eacute;RICA) m&aacute;s IVA en ciudad y $35(TREINTA D&Oacute;LARES DE LOS ESTADOS UNIDOS DE AM&Eacute;RICA) m&aacute;s IVA en zonas for&aacute;neas (aplica solo para Quito y Guayaquil).&nbsp;</li>
<li>Costo de la hora adicional despu&eacute;s de la primera hora de atenci&oacute;n $10(DIEZ D&Oacute;LARES DE LOS ESTADOS UNIDOS DE AM&Eacute;RICA) m&aacute;s IVA.&nbsp;</li>
<li>Todos los planes Pro y PYME&nbsp;(seg&uacute;n la oferta&nbsp;establecida por Netlife),&nbsp;incluyen&nbsp;Netlife&nbsp;Assistance&nbsp;Pro,&nbsp;un servicio de asistencia especializada en problemas t&eacute;cnicos e inform&aacute;ticos, disponible para 5 (CINCO) usuarios.&nbsp;Para acceder a &eacute;l es necesario ingresar dentro de la secci&oacute;n &ldquo;Netlife Access&rdquo; en la p&aacute;gina web de Netlife o a store.netlife.net.ec.&nbsp;&nbsp;</li>
</ul>');
UPDATE DB_COMERCIAL.ADMI_PRODUCTO ADPR
SET ADPR.TERMINO_CONDICION = bada,
ADPR.FRECUENCIA = 'Mensual'
where ADPR.ID_PRODUCTO =1262;

COMMIT;
END;

/
--=================================================================
declare
    bada clob:=' ';
begin

DBMS_LOB.APPEND(bada, '<p style="text-align: center;"><strong>T&Eacute;RMINOS Y CONDICIONES DE CONSTRUCTOR WEB</strong></p>
<p> Constructor Web es un servicio que te permite construir tu propia p&aacute;gina web, tener 1(UN) dominio propio y 5(CINCO) cuentas de correo asociadas a este dominio. ​ Adem&aacute;s de, asesor&iacute;a t&eacute;cnica en l&iacute;nea las 24(VEINTI CUATRO) horas v&iacute;a telef&oacute;nica o web por&nbsp;<a href="https://store.netlife.net.ec/"><em>store.netlife.net.ec</em></a>&nbsp;.​ El acceso al servicio es posible desde&nbsp;<a href="https://store.netlife.net.ec/">store.netlife.net.ec</a>&nbsp;</p>
<ul>
<li>Se incluye el servicio de dise&ntilde;o de la p&aacute;gina web por parte del equipo de dise&ntilde;o bajo solicitud del usuario y sujeto al env&iacute;o de la informaci&oacute;n relevante para su creaci&oacute;n.&nbsp;</li>
<li>El servicio incluye hasta 5(CINCO) p&aacute;ginas de contenido, formulario de contacto para recibir comunicaci&oacute;n de los visitantes a un correo especificado, links a las redes sociales, mapa de Google interactivo, conexi&oacute;n con Google&nbsp;Analytics.&nbsp;</li>
<li>El tiempo de entrega/publicaci&oacute;n estimado&nbsp;es de 5(CINCO) d&iacute;as h&aacute;biles, pero est&aacute; sujeto al env&iacute;o oportuno de informaci&oacute;n del cliente, as&iacute; como del volumen de material recibido. ​&nbsp;</li>
<li>En cuanto al dominio: La propiedad del dominio est&aacute; condicionada a un tiempo de permanencia m&iacute;nima de 12 meses y se renueva anualmente.&nbsp;</li>
<li>En caso de cancelarlo antes de los 12 meses de cualquiera de sus per&iacute;odos de vigencia y renovaci&oacute;n, el cliente deber&aacute; pagar el valor proporcional, de acuerdo con el tiempo de vigencia que resta por cubrir.&nbsp;</li>
<li>Es responsabilidad del cliente tomar las medidas necesarias para almacenar la informaci&oacute;n colocada en su p&aacute;gina web.&nbsp;</li>
<li>El servicio incluye&nbsp;Webmail: Administraci&oacute;n de correos, carpetas, y filtros con una interfaz intuitiva y f&aacute;cil de utilizar proporcionada por&nbsp;Roundcube. Se puede agregar cualquier cuenta IMAP/POP para tener una &uacute;nica interfaz.&nbsp;</li>
<li>El servicio tambi&eacute;n incluye 5 cuentas de correo: Capacidad de 1 Gb de almacenamiento por cuenta. Archivos adjuntos: hasta 25 Mb por correo enviado.&nbsp;</li>
<li>El servicio no incluye mantenimientos programados a las plataformas que soportan al correo y mantenimientos no programados para solventar situaciones cr&iacute;ticas. ​&nbsp;</li>
<li>Netlife Constructor Web tiene un precio de $14,99(CATORCE D&Oacute;LARES DE LOS&nbsp;ESTADOS UNIDOS DE AM&Eacute;RICA CON 99/100) m&aacute;s IVA&nbsp;mensual, que se a&ntilde;ade a planes de Internet de Netlife.&nbsp;</li>
<li>El primer mes de servicio, el cliente no pagar&aacute; el valor total, sino el valor proporcional por el tiempo que haya recibido el servicio de acuerdo con su ciclo de facturaci&oacute;n (Ciclo 1: Del 1 al 30 del mes o Ciclo 2: Del 15 al 14 del mes siguiente).&nbsp;</li>
</ul>
<p>Navegadores Soportados: Windows Vista, 7, y 8 | IE 9.0 en adelante | Firefox versi&oacute;n 19 en adelante. | Google Chrome versi&oacute;n 25 en adelante | Windows 10 | Edge 12 en adelante | Mac OS X 10.4, 10.5, y 10.6 | Firefox versi&oacute;n 19 en adelante | Safari versi&oacute;n 4.0 en adelante&nbsp;</p>
<ul>
<li>Se considera &ldquo;spam&rdquo; la pr&aacute;ctica de enviar mensajes de correo electr&oacute;nico no deseados, a menudo con contenido comercial, en grandes cantidades a los usuarios, sin darles la opci&oacute;n de darse de baja o excluirse de una lista de distribuci&oacute;n.&nbsp;</li>
<li>Por lo anterior, queda prohibido que el cliente use el correo para estos fines.&nbsp;</li>
</ul>
<p><strong>En caso de cualquier violaci&oacute;n a estas Pol&iacute;ticas, se proceder&aacute; a tomar una de las siguientes medidas:&nbsp;</strong>&nbsp;</p>
<ol>
<li>Suspender/Bloquear la cuenta por un lapso de 72 horas.&nbsp;</li>
<li>Suspender/Bloquear la cuenta por un lapso de 144 horas.&nbsp;</li>
<li>Suspender/Bloquear todo tr&aacute;fico del dominio y se iniciar&aacute; el proceso de baja de servicio.&nbsp;</li>
</ol>
<ul>
<li>Los planes PYME (seg&uacute;n la oferta establecida por Netlife) pueden incluir el servicio de Constructor Web, para construir tu propia p&aacute;gina web, tener 1(UN) dominio propio y 5(CINCO) cuentas de correo asociadas a este dominio. Adem&aacute;s de, asesor&iacute;a t&eacute;cnica en l&iacute;nea las 24(VEINTI CUATRO) horas v&iacute;a telef&oacute;nica, chat o web. Para acceder al servicio es necesario ingresar dentro de la secci&oacute;n &ldquo;Netlife Access&rdquo; en la p&aacute;gina web de Netlife o a store.netlife.net.ec.</li>
</ul>');
UPDATE DB_COMERCIAL.ADMI_PRODUCTO ADPR
SET ADPR.TERMINO_CONDICION = bada,
ADPR.FRECUENCIA = 'Mensual'
where ADPR.ID_PRODUCTO =1263;

COMMIT;
END;

/
--=================================================================
declare
    bada clob:=' ';
begin

DBMS_LOB.APPEND(bada, '<p style="text-align: center;"><strong>T&Eacute;RMINOS Y CONDICIONES DE WIFI DUAL BAND PREMIUM Y AP EXTENDER DUAL BAND:</strong></p>
<p style="text-align: justify;">El equipo es propiedad de MEGADATOS S.A. y cuenta con una garant&iacute;a de 1(UN) a&ntilde;o por defectos de f&aacute;brica. Al finalizar la prestaci&oacute;n del servicio el cliente deber&aacute; entregarlo en las oficinas de MEGADATOS. En caso de que el cliente no lo devolviere, se detecte mal uso o da&ntilde;os, el costo total del equipo por reposici&oacute;n ser&aacute; facturado al cliente. En caso del router WiFi Dual Band Premium es de $175,00 (m&aacute;s IVA) y del AP Extender Dual Band es de $75.00 (m&aacute;s IVA).</p>
<p style="text-align: justify;">El cliente conoce y acepta que, para garantizar la calidad del servicio, estos equipos ser&aacute;n administrado por NETLIFE mientras dure la prestaci&oacute;n del servicio.</p>
<p style="text-align: justify;">El equipo WiFi provisto por NETIFE tiene puertos al&aacute;mbricos que permiten la utilizaci&oacute;n &oacute;ptima de la velocidad ofertada en el plan contratado, adem&aacute;s cuenta con conexi&oacute;n WiFi a una frecuencia de 5Ghz que permite una velocidad m&aacute;xima de 150Mbps a una distancia de 3 metros y pueden conectarse equipos a una distancia de hasta 12 metros en condiciones normales, sin embargo, la distancia de cobertura var&iacute;a seg&uacute;n la cantidad y tipo de paredes, obst&aacute;culos e interferencia que se encuentren en el entorno. El cliente conoce y acepta que la tecnolog&iacute;a WiFi pierde potencia a mayor distancia y por lo tanto se reducir&aacute; la velocidad efectiva a una mayor distancia de conexi&oacute;n del equipo.</p>');
UPDATE DB_COMERCIAL.ADMI_PRODUCTO ADPR
SET ADPR.TERMINO_CONDICION = bada,
ADPR.FRECUENCIA = 'Mensual'
where ADPR.ID_PRODUCTO =1357;

COMMIT;
END;

/
--=================================================================
declare
    bada clob:=' ';
begin

DBMS_LOB.APPEND(bada, '<p style="text-align: center;"><strong>T&Eacute;RMINOS Y CONDICIONES DE </strong><strong>PUNTO CABLEADO ETHERNET</strong></p>
<ul>
<li>Punto Cableado Ethernet es un producto que contempla la instalaci&oacute;n o acondicionamiento de un (1) punto cableado a un (1) dispositivo del cliente, para acceso a internet directo por cable. El producto tiene un metraje m&aacute;ximo de 30mts e incluye&nbsp;para su acondicionamiento 2 conectores (cat6) y 10 metros de canaleta.&nbsp;&nbsp;&nbsp;</li>
<li>Por la contrataci&oacute;n del producto el cliente realiza un pago &uacute;nico de $35,00+iva, que se incluir&aacute; en su factura.&nbsp;Este producto no tiene un tiempo m&iacute;nimo de permanencia y no es sujeto de traslado.&nbsp;</li>
<li>La contrataci&oacute;n del servicio est&aacute; limitada a 3 puntos cableados por punto del cliente.&nbsp;</li>
<li>En caso de que el cliente requiera que se le retire el punto cableado, se le cobrar&aacute; el valor de la visita t&eacute;cnica programada cuyo valor se puede encontrar en la secci&oacute;n de atenci&oacute;n al cliente de:&nbsp;<a href="https://www.netlife.ec/">https://www.netlife.ec</a></li>
<li>En los casos de soporte imputables al cliente se cobrar&aacute; el costo de los materiales utilizados y la visita t&eacute;cnica programada.&nbsp;</li>
</ul>');
UPDATE DB_COMERCIAL.ADMI_PRODUCTO ADPR
SET ADPR.TERMINO_CONDICION = bada,
ADPR.FRECUENCIA = 'Unica'
where ADPR.ID_PRODUCTO =1332;

COMMIT;
END;

/
--=================================================================
declare
    bada clob:=' ';
begin

DBMS_LOB.APPEND(bada, '<p style="text-align: center;"><strong>T&Eacute;RMINOS Y CONDICIONES DE PARAMOUNT+</strong></p>
<ul>
<li>Paramount+&nbsp;es una plataforma de entretenimiento por&nbsp;streaming&nbsp;que incluye episodios de series y otras producciones de canales de&nbsp;Showtime, CBS,&nbsp;Nickelodeon, MTV,&nbsp;Comedy&nbsp;Central y todas las pel&iacute;culas distribuidas por Paramount&nbsp;Pictures.&nbsp;</li>
<li>El cliente tendr&aacute; acceso a la plataforma mediante enlace Web&nbsp;(<a href="https://www.paramountmas.com/ec/">https://www.paramountmas.com/ec/</a>)&nbsp;o a trav&eacute;s del aplicativo m&oacute;vil de &ldquo;Paramount+&rdquo;.&nbsp;&nbsp;</li>
<li>Para acceder a&nbsp;la plataforma de Paramount+,&nbsp;recibir&aacute; un&nbsp;usuario y&nbsp;contrase&ntilde;a de acceso mediante correo electr&oacute;nico y/o&nbsp;sms&nbsp;al correo&nbsp;y n&uacute;mero de contacto&nbsp;registrados&nbsp;en su contrato&nbsp;con Netlife.&nbsp; Este correo debe ser un correo electr&oacute;nico v&aacute;lido. Es de absoluta responsabilidad del cliente verificar que el correo no se encuentre alojado en la carpeta de correo no deseado(spam)&nbsp;y en caso de requerirlo, podr&aacute; solicitar el reenv&iacute;o de&nbsp;sus credenciales contact&aacute;ndose al 3920000&nbsp;o&nbsp;a trav&eacute;s de la opci&oacute;n&nbsp;&ldquo;Olvid&oacute; su contrase&ntilde;a&rdquo;&nbsp;tanto en&nbsp;el enlace&nbsp;web&nbsp;como&nbsp;en&nbsp;el aplicativo m&oacute;vil.&nbsp;</li>
<li>En caso de requerirlo, el cliente podr&aacute; recibir soporte respecto cuestiones t&eacute;cnicas relacionadas con la plataforma como reproducci&oacute;n de video, contenidos no disponibles, errores en las im&aacute;genes de los contenidos, entre otros; a trav&eacute;s, del chat y&nbsp;help&nbsp;center email de la plataforma de Paramount+; as&iacute; como, la secci&oacute;n de Preguntas Frecuentes en la misma plataforma. Adicional, podr&aacute; contactarse al 392000 para recibir soporte.&nbsp;&nbsp;</li>
<li>El precio de Paramount+ como servicio adicional es de $4.02(CUATRO&nbsp;D&Oacute;LARES DE&nbsp;LOS ESTADOS UNIDOS DE AM&Eacute;RICA CON&nbsp;02/100) mensual m&aacute;s IVA.&nbsp;</li>
<li>El primer mes de servicio, el cliente no pagar&aacute; el valor total, sino el valor proporcional por el tiempo que haya recibido el servicio de acuerdo con su ciclo de facturaci&oacute;n (Ciclo 1: Del 1 al 30 del mes o Ciclo 2: Del 15 al 14 del mes siguiente).&nbsp;</li>
<li>Paramount+ es compatible con dispositivos: Android &amp; Amazon&nbsp;Fire&nbsp; Table/TV (5.0&nbsp;Lollipop&nbsp;API 21&nbsp;y superiores), Apple&nbsp; (iOS 11.2&nbsp;/&nbsp;TvOs&nbsp;12)&nbsp;Compatible con iPhone, iPad y iPod&nbsp;touch., Web Browsers, Chromecast y&nbsp;Ariplay.&nbsp;</li>
<li>Al contratar el servicio el cliente podr&aacute; visualizar el contenido hasta en 5 dispositivos; de los cuales, en hasta 2 dispositivos podr&aacute; ver en simult&aacute;neo un mismo programa, serie, pel&iacute;cula, etc.&nbsp;</li>
<li>La entrega de este producto no incluye el servicio de instalaci&oacute;n&nbsp;del mismo&nbsp;en ning&uacute;n dispositivo. El cliente es responsable de la instalaci&oacute;n y configuraci&oacute;n del producto en sus dispositivos y usuarios.&nbsp;</li>
<li>El cliente puede realizar la cancelaci&oacute;n de su suscripci&oacute;n a trav&eacute;s del&nbsp;call&nbsp;center&nbsp;o centros de atenci&oacute;n&nbsp;el&nbsp;30&nbsp;o&nbsp;14 de cada mes seg&uacute;n su ciclo de facturaci&oacute;n.&nbsp;</li>
<li>En caso de que el cliente sea beneficiario de una promoci&oacute;n/descuento, el cliente entiende que el incumplimiento de los lineamientos establecidos para recibir dicha promoci&oacute;n/descuento resulten en el cobro de los beneficios entregados al mismo.&nbsp;</li>
<li>Los t&eacute;rminos de uso de la plataforma, ser&aacute;n aquellos definidos por&nbsp;Viacom&nbsp;International&nbsp;Inc&nbsp;,&nbsp;detallados en el siguiente link https://www.paramountmas.com/ec/terminos-de-uso.&nbsp;&nbsp;</li>
<li>La pol&iacute;tica de privacidad de la plataforma, ser&aacute; aquella definida por&nbsp;Viacom&nbsp;International&nbsp;Inc&nbsp;,&nbsp;en el siguiente link&nbsp;<a href="https://www.paramountmas.com/ec/politica-de-privacidad">https://www.paramountmas.com/ec/politica-de-privacidad</a></li>
<li>El control de contenido de Paramount+ deber&aacute; ser realizado por sus contratantes y/o adultos delegados para que puedan definir los criterios que eviten la visualizaci&oacute;n de contenido no apto de acuerdo con la edad del ni&ntilde;o, ni&ntilde;a o adolescente.&nbsp;&nbsp;</li>
<li>La aplicaci&oacute;n tiene una funci&oacute;n preliminar de&nbsp;control parental&nbsp;para garantizar que el contratante y/o adultos delegados ser&aacute;n responsables de los criterios de visualizaci&oacute;n.&nbsp;</li>
</ul>
<p style="text-align: center;"><strong>Promoci&oacute;n</strong></p>
<ul>
<li>20% (VEINTE&nbsp;POR CIENTO) de descuento en la 1 primera factura del servicio del&nbsp;1&nbsp;al 31&nbsp;de&nbsp;agosto&nbsp;del 2021, aplica solo para clientes que agreguen este servicio como adicional a su plan de internet dentro de la fecha estipulada.&nbsp;</li>
<li>Precio despu&eacute;s de finalizar la promoci&oacute;n: $4.50 (CUATRO D&Oacute;LARES DE LOS ESTADOS UNIDOS DE AM&Eacute;RICA CON 50/100) mensual&nbsp;incluido impuestos.&nbsp;&nbsp;</li>
</ul>');
UPDATE DB_COMERCIAL.ADMI_PRODUCTO ADPR
SET ADPR.TERMINO_CONDICION = bada,
ADPR.FRECUENCIA = 'Mensual'
where ADPR.ID_PRODUCTO =1320;

COMMIT;
END;

/
--=================================================================
declare bada
clob := ' ';

BEGIN
    dbms_lob.append(bada, '<p style="text-align: center;"><strong>T&Eacute;RMINOS Y CONDICIONES DE NOGGIN+</strong>&nbsp;</p>
<ul>
<li>Noggin&nbsp;es una aplicaci&oacute;n para que los m&aacute;s peque&ntilde;os vean episodios de las mejores series de Nick Jr. en un ambiente divertido, educativo y seguro.&nbsp;Ofrece una amplia y diversa colecci&oacute;n de videos, juegos, m&uacute;sica de los&nbsp;shows&nbsp;favoritos de Nick Jr. como&nbsp;Paw&nbsp;Patrol,&nbsp;Shimmer&nbsp;y&nbsp;Shine, Dora la Exploradora, &iexcl;y muchos m&aacute;s!</li>
<li>Para ni&ntilde;os de preescolar entre&nbsp;2 a 6 a&ntilde;os.</li>
<li>El cliente tendr&aacute; acceso a la plataforma mediante enlace Web (<a href="https://www.nogginla.com/">https://www.nogginla.com</a>)o a trav&eacute;s del aplicativo m&oacute;vil de &ldquo;Noggin&rdquo;.&nbsp;&nbsp;</li>
<li>Para acceder a la plataforma de&nbsp;Noggin,&nbsp;recibir&aacute; un usuario y contrase&ntilde;a de acceso mediante correo electr&oacute;nico y/o&nbsp;sms&nbsp;al correo y n&uacute;mero de contacto registrados en su contrato con Netlife.&nbsp; Este correo debe ser un correo electr&oacute;nico v&aacute;lido. Es de absoluta responsabilidad del cliente verificar que el correo no se encuentre alojado en la carpeta de correo no deseado(spam) y en caso de requerirlo, podr&aacute; solicitar el reenv&iacute;o de sus credenciales contact&aacute;ndose al 3920000 o a trav&eacute;s de la opci&oacute;n &ldquo;Olvid&oacute; su contrase&ntilde;a&rdquo; tanto en el enlace web como en el aplicativo m&oacute;vil.&nbsp;</li>
<li>En caso de requerirlo, el cliente podr&aacute; recibir soporte respecto cuestiones t&eacute;cnicas relacionadas con la plataforma como reproducci&oacute;n de video, contenidos no disponibles, errores en las im&aacute;genes de los contenidos, entre otros; a trav&eacute;s, del chat y&nbsp;help&nbsp;center email de la plataforma de&nbsp;Noggin; as&iacute; como, la secci&oacute;n de Preguntas Frecuentes en la misma plataforma. Adicional, podr&aacute; contactarse al 392000 para recibir soporte.&nbsp;&nbsp;</li>
<li>El precio de&nbsp;Noggin&nbsp;como servicio adicional es de $2.23(DOS&nbsp;D&Oacute;LARES DE LOS ESTADOS UNIDOS DE AM&Eacute;RICA CON 23/100) mensual m&aacute;s IVA.&nbsp;</li>
<li>El primer mes de servicio, el cliente no pagar&aacute; el valor total, sino el valor proporcional por el tiempo que haya recibido el servicio de acuerdo con su ciclo de facturaci&oacute;n (Ciclo 1: Del 1 al 30 del mes o Ciclo 2: Del 15 al 14 del mes siguiente).&nbsp;</li>
<li>Noggin&nbsp;est&aacute; disponible para ver en Tabletas y Smartphones, iOS, Android y Computadores.&nbsp;</li>
<li>Al contratar el servicio el cliente podr&aacute; visualizar el contenido hasta en 5 dispositivos; de los cuales, en hasta 2 dispositivos podr&aacute; ver en simult&aacute;neo un mismo programa, serie, juegos, etc.&nbsp;</li>
<li>La entrega de este producto no incluye el servicio de instalaci&oacute;n&nbsp;del mismo&nbsp;en ning&uacute;n dispositivo. El cliente es responsable de la instalaci&oacute;n y configuraci&oacute;n del producto en sus dispositivos y usuarios.&nbsp;</li>
<li>El cliente puede realizar la cancelaci&oacute;n de su suscripci&oacute;n a trav&eacute;s del&nbsp;call&nbsp;center&nbsp;o centros de atenci&oacute;n&nbsp;el&nbsp;30&nbsp;o&nbsp;14 de cada mes seg&uacute;n su ciclo de facturaci&oacute;n.&nbsp;</li>
<li>En caso de que el cliente sea beneficiario de una promoci&oacute;n/descuento, el cliente entiende que el incumplimiento de los lineamientos establecidos para recibir dicha promoci&oacute;n/descuento resulten en el cobro de los beneficios entregados al mismo.&nbsp;</li>
<li>Los t&eacute;rminos de uso de la plataforma, ser&aacute;n aquellos definidos por&nbsp;Viacom&nbsp;International&nbsp;Inc&nbsp;,&nbsp;detallados en el siguiente link&nbsp;<a href="https://www.nickelodeon.la/legal/373cyf/terminos-y-condiciones">https://www.nickelodeon.la/legal/373cyf/terminos-y-condiciones</a>.&nbsp;&nbsp;</li>
<li>La pol&iacute;tica de privacidad de la plataforma, ser&aacute; aquella definida por&nbsp;Viacom&nbsp;International&nbsp;Inc&nbsp;,&nbsp;en el siguiente&nbsp;<u>link&nbsp;</u><a href="https://www.nickelodeon.la/legal/zs8ejr/politica-de-privacidad">https://www.nickelodeon.la/legal/zs8ejr/politica-de-privacidad</a></li>
<li>El control de contenido de&nbsp;Noggin&nbsp;deber&aacute; ser realizado&nbsp;por&nbsp;sus contratantes y/o adultos delegados para que puedan definir los criterios que eviten la visualizaci&oacute;n de contenido no apto&nbsp;de acuerdo con&nbsp;la edad del ni&ntilde;o, ni&ntilde;a o adolescente.&nbsp;&nbsp;</li>
</ul>
<p>La aplicaci&oacute;n&nbsp;tiene una funci&oacute;n&nbsp;preliminar&nbsp;de&nbsp;verificaci&oacute;n de edad&nbsp;de nacimiento&nbsp;para&nbsp;garantizar que&nbsp;el contratante y/o adultos delegados ser&aacute;n responsables&nbsp;de los criterios de visualizaci&oacute;n.</p>'
    );
    UPDATE db_comercial.admi_producto adpr
    SET
        adpr.termino_condicion = bada,
        adpr.frecuencia = 'Mensual'
    WHERE
        adpr.id_producto = 1321;

    COMMIT;
END;
/
