--Creación de las plantillas que usan ECUCERT para la notificación al cliente.

SET DEFINE OFF 
INSERT INTO DB_COMUNICACION.ADMI_PLANTILLA (ID_PLANTILLA, NOMBRE_PLANTILLA, CODIGO, MODULO, PLANTILLA, ESTADO, FE_CREACION, USR_CREACION, FE_ULT_MOD, USR_ULT_MOD, EMPRESA_COD)
VALUES (DB_COMUNICACION.SEQ_ADMI_PLANTILLA.NEXTVAL,'NOTIFICACIÓN_DE_IP_REALIZANDO_ACTIVIDADES_SOSPECHOSAS','ACTSOS','SOPORTE',
TO_CLOB('<html>
  <head>
  </head>
  <body>
    <p><strong>NOTIFICACI&Oacute;N DE IP REALIZANDO ACTIVIDADES SOSPECHOSAS</strong> <br />======================================================</p>
    <p>Estimado Cliente,</p>
    <p style="text-align: justify;">En ticket num <strong>{{ticket_ecucert}}</strong> enviado a Telconet por ARCOTEL (Agencia de Regulaci&oacute;n y Control de las Telecomunicaciones), a trav&eacute;s de su departamento EcuCERT, le informamos que la IP <strong>{{ip}}</strong> ha sido reportada realizando <strong>actividades sospechosas</strong>, a trav&eacute;s de conexiones hacia la IP <strong>{{ipDestino}}</strong>, perteneciente a <strong>{{ip}}</strong> por el/los puerto(s): <strong>{{puerto}}</strong>.</p>
    <p style="text-align: justify;">Telconet, con el fin de precautelar la disponibilidad e integridad de su servicio, el de nuestros clientes y el de nuestra infraestructura, solicita su colaboraci&oacute;n revisando si las siguientes conexiones son leg&iacute;timas: <strong>{{ipDestino}}</strong></p>    ') 
    || TO_CLOB('
	<p style="text-align: justify;">En caso de no serlo, le sugerimos revisar el equipo en busca de malware que pudiera estar comprometiendo al equipo, y desinfectarlo completamente. En caso de desconocer si el equipo est&aacute; infectado, recomendamos reinstalarlo completamente; adem&aacute;s de mantener siempre el equipo con un antivirus actualizado.</p>
    <p style="text-align: justify;">El plazo m&aacute;ximo que otorga el Departamento de Seguridad L&oacute;gica ante esta incidencia es <strong>5 d&iacute;as laborables</strong>, posterior a &eacute;ste, y si no se ha recibido respuesta a la presente notificaci&oacute;n de su parte, se proceder&aacute; al cierre de la incidencia y registro seg&uacute;n corresponda para el ente regulador.</p>
    <p style="text-align: justify;">Quedamos a la espera de su pronta respuesta, y agradecemos la atenci&oacute;n prestada a la presente.</p>
    <p>Para una oportuna gesti&oacute;n del incidente, favor dirigir sus respuestas al correo <strong>&lt;soporte_seguridad@telconet.ec&gt;</strong></p>
    <p>Atentamente,</p>
    <p>Soporte T&eacute;cnico <br />Telconet S.A.</p>
  </body>
</html>'),
'Activo',SYSDATE,'nnaulal',null,null,null);

SET DEFINE OFF 
INSERT INTO DB_COMUNICACION.ADMI_PLANTILLA (ID_PLANTILLA, NOMBRE_PLANTILLA, CODIGO, MODULO, PLANTILLA, ESTADO, FE_CREACION, USR_CREACION, FE_ULT_MOD, USR_ULT_MOD, EMPRESA_COD)
VALUES (DB_COMUNICACION.SEQ_ADMI_PLANTILLA.NEXTVAL,'NOTIFICACIÓN_DE_IP_PERTENECIENTE_A_BOTNET','BOTNETS','SOPORTE',
TO_CLOB('<html>
  <head>
  </head>
  <body>
    <p><strong>NOTIFICACI&Oacute;N DE IP PERTENECIENTE A BOTNET</strong><br />=========================================</p>
    <p>Estimado cliente,</p>
    <p style="text-align: justify;">En reporte enviado a Telconet por ARCOTEL (Agencia de Regulaci&oacute;n y Control de las Telecomunicaciones), a trav&eacute;s de su departamento EcuCERT, le informamos que la IP <strong>{{ip}}</strong> podr&iacute;a estar formando parte de una BOTNET, nombre gen&eacute;rico que se le da a un grupo de equipos comprometidos mediante malware, que puede ser de diferentes tipos. Esta condici&oacute;n le permite a un agente malicioso controlar completamente y de forma remota al equipo infectado, con el fin de realizar actividades il&iacute;citas, como por ejemplo env&iacute;o masivo de correos no deseados (SPAM), ataques DDoS a servidores de terceros, fraudes en-l&iacute;nea, entre otros.</p>
    <p style="text-align: justify;">Telconet, con el fin de precautelar la disponibilidad e integridad de su servicio, el de nuestros clientes y el de nuestra infraestructura, solicita su colaboraci&oacute;n revisando el equipo en busca de malware que pudiera estar comprometiendo al equipo, y desinfectarlo completamente. En caso de desconocer si el equipo est&aacute; infectado, recomendamos reinstalarlo completamente; adem&aacute;s de mantener siempre el equipo con un antivirus actualizado.</p>    ') 
    || TO_CLOB('
	<p style="text-align: justify;">Si existe una red interna que sale a trav&eacute;s de la IP ofensora, todos los equipos de esta red deben ser revisados, puesto, muy probablemente, varios de los equipos de esta red se encuentren infectados.</p>
    <p style="text-align: justify;">M&aacute;s informaci&oacute;n sobre detecci&oacute;n de m&aacute;quinas infectadas puede ser encontrado en los siguientes enlaces:</p>
    <p>* [http://www.abuseat.org/advanced.html](http://www.abuseat.org/advanced.html)</p>
    <p style="text-align: justify;">El plazo m&aacute;ximo que otorga el Departamento de Seguridad L&oacute;gica ante esta incidencia es <strong>5 d&iacute;as laborables</strong>, posterior a &eacute;ste, y si no se ha recibido respuesta a la presente notificaci&oacute;n de su parte, se proceder&aacute; a comunicar al ente regulador.</p>
    <p>Quedamos a la espera de su pronta respuesta, y agradecemos la atenci&oacute;n prestada a la presente.</p>
    <p>Para una oportuna gesti&oacute;n del incidente, favor dirigir sus respuestas al correo <strong>&lt;soporte_seguridad@telconet.ec&gt;</strong></p>
    <p>Atentamente,</p>
    <p>Soporte T&eacute;cnico <br />Telconet S.A.</p>
  </body>
</html>'),
'Activo',SYSDATE,'nnaulal',null,null,null);

SET DEFINE OFF 
INSERT INTO DB_COMUNICACION.ADMI_PLANTILLA (ID_PLANTILLA, NOMBRE_PLANTILLA, CODIGO, MODULO, PLANTILLA, ESTADO, FE_CREACION, USR_CREACION, FE_ULT_MOD, USR_ULT_MOD, EMPRESA_COD)
VALUES (DB_COMUNICACION.SEQ_ADMI_PLANTILLA.NEXTVAL,'NOTIFICACIÓN_OPEN_CHARGEN','CHARGEN','SOPORTE',
TO_CLOB('<html>
  <head>
  </head>
  <body>
    <p><strong>NOTIFICACI&Oacute;N OPEN CharGEN</strong><br />=========================</p>
    <p>Estimado cliente,</p>
    <p style="text-align: justify;">En reporte enviado a Telconet por ARCOTEL (Agencia de Regulaci&oacute;n y Control de las Telecomunicaciones), a trav&eacute;s de su departamento EcuCERT, le informamos que la IP {{ip}} asignada a {{login}} presenta el servicio **CharGEN habilitado y accesible desde Internet por el puerto TCP y UDP 19**. CharGEN es un protocolo utilizado para fines de prueba y depuraci&oacute;n que utiliza el puerto 19 en TCP y UDP; al abrir una conexi&oacute;n TCP, el servidor env&iacute;a caracteres arbitrarios al host de conexi&oacute;n hasta que &eacute;ste cierre la conexi&oacute;n. Si la conexi&oacute;n es UDP, el servidor env&iacute;a un datagrama UDP que contiene un n&uacute;mero aleatorio de caracteres cada vez que recibe un datagrama desde el host. Este comportamiento tiene el potencial de ser utilizado en ataques de amplificaci&oacute;n de Denegaci&oacute;n de Servicio (DoS) cuando el servicio se encuentra accesible desde el internet.</p>
    ') 
    || TO_CLOB('
	<p style="text-align: justify;">Telconet, con el fin de precautelar la disponibilidad e integridad de su servicio, el de nuestros<br />clientes y el de nuestra infraestructura, solicita su colaboraci&oacute;n deshabilitando el servicio CharGEN, o bloquear completamente la recepci&oacute;n de tr&aacute;fico por el puerto ofensor, en caso de no ser utilizado. Si el servicio es necesario, restringir las conexiones TCP y UDP al puerto 19 &uacute;nicamente desde IPs de confianza.</p>
    <p style="text-align: justify;">En caso de que este servicio/puerto se encuentre habilitado sin su conocimiento, considerar que el equipo podr&iacute;a encontrarse comprometido; de ser as&iacute;, una revisi&oacute;n m&aacute;s minuciosa debe ser realizada para determinar las causas de la incidencia. Si existe una red interna nateada a trav&eacute;s de la IP ofensora, todos los equipos de esta red deben ser revisados, con el fin de localizar al equipo que presente el servicio activo.</p>
    <p style="text-align: justify;">M&aacute;s informaci&oacute;n sobre la vulnerabilidad OPEN CharGEN y c&oacute;mo configurar de manera segura los dispositivos afectados puede ser encontrada en los siguientes enlaces:</p>
    <p>* [http://www.ecucert.gob.ec/open_chargen.html#ANCHOR_Box1](http://www.ecucert.gob.ec/open_chargen.html#ANCHOR_Box1)<br />* [http://www.windowsnetworking.com/articles-tutorials/windows-7/Windows-7-Simple-TCPIP-<br />Services-What-How.html](http://www.windowsnetworking.com/articles-tutorials/windows-7/Windows-7-Simple-TCPIP-Services-What-How.html)<br />* [https://tools.ietf.org/html/rfc864](https://tools.ietf.org/html/rfc864)</p>
    ')
    || TO_CLOB('
    <p style="text-align: justify;">El plazo m&aacute;ximo que otorga el Departamento de Seguridad L&oacute;gica ante esta incidencia es <strong>5 d&iacute;as laborables</strong>, posterior a &eacute;ste, y si no se ha recibido respuesta a la presente notificaci&oacute;n de su parte, se proceder&aacute; al bloqueo de recepci&oacute;n de tr&aacute;fico por la IP y puerto ofensor (19 TCP y UDP).</p>
    <p style="text-align: justify;">Quedamos a la espera de su pronta respuesta, y agradecemos la atenci&oacute;n prestada a la presente.</p>
    <p>Para una oportuna gesti&oacute;n del incidente, favor dirigir sus respuestas al correo <strong>&lt;soporte_seguridad@telconet.ec&gt;</strong></p>
    <p>Atentamente,</p>
    <p>Soporte T&eacute;cnico <br />Telconet S.A</p>
  </body>
</html>'),
'Activo',SYSDATE,'nnaulal',null,null,null);

SET DEFINE OFF 
INSERT INTO DB_COMUNICACION.ADMI_PLANTILLA (ID_PLANTILLA, NOMBRE_PLANTILLA, CODIGO, MODULO, PLANTILLA, ESTADO, FE_CREACION, USR_CREACION, FE_ULT_MOD, USR_ULT_MOD, EMPRESA_COD)
VALUES (DB_COMUNICACION.SEQ_ADMI_PLANTILLA.NEXTVAL,'NOTIFICACIÓN_OPEN_ELASTICSEARCH','ELASTICSE','SOPORTE',
TO_CLOB('<html>
  <head>
  </head>
  <body>
    <p><strong>NOTIFICACI&Oacute;N Open Elasticsearch</strong><br />===============================</p>
    <p>Estimado cliente,</p>
    <p style="text-align: justify;">En reporte enviado a Telconet por ARCOTEL (Agencia de Regulaci&oacute;n y Control de las Telecomunicaciones), a trav&eacute;s de su departamento EcuCERT, le informamos que la IP<strong> {{ip}}</strong> asignada a {Nombre del cliente/Login} est&aacute; prestando el servicio de <strong>Elasticsearch, accesible</strong><br /><strong>desde el internet a trav&eacute;s del puerto TCP 9200</strong>. Este servicio permite indexar y realizar b&uacute;squedas sobre data y logs de dispositivos y es utilizado en conjunto con Logstash y Kibana como un SIEM. Debido a que Elasticsearch no admite autenticaci&oacute;n de usuarios para restringir el acceso al servicio, es posible que cualquier entidad pueda tener acceso y control total del mismo.</p>  
    ') 
    || TO_CLOB('
	<p style="text-align: justify;">Telconet, con el fin de precautelar la disponibilidad e integridad de su servicio, el de nuestros clientes y el de nuestra infraestructura, solicita su colaboraci&oacute;n restringiendo el acceso al servicio s&oacute;lo a IPs de confianza y que necesiten acceder al mismo; en caso de que el servicio no est&eacute; siendo utilizado, desactivarlo por completo. En caso de que este servicio/puerto se encuentre habilitado sin su conocimiento, considerar que el equipo podr&iacute;a encontrarse comprometido; de ser as&iacute;, una revisi&oacute;n m&aacute;s minuciosa debe ser realizada para determinar las causas de la incidencia. Si existe una red interna nateada a trav&eacute;s de la IP ofensora, todos los equipos de esta red deben ser revisados, con el fin de localizar al equipo que presente el servicio habilitado.</p>
    <p style="text-align: justify;">M&aacute;s informaci&oacute;n sobre la vulnerabilidad Open Elasticsearch y c&oacute;mo configurar de manera segura los dispositivos afectados puede ser encontrada en los siguientes enlaces:</p>
    ')
    || TO_CLOB('
    <p>* [http://www.ecucert.gob.ec/open---elastic-search.html](http://www.ecucert.gob.ec/open---elastic-search.html)<br />* [https://www.elastic.co/blog/found-elasticsearch-security#suggested-solutions](https://www.elastic.co/blog/found-elasticsearch-security#suggested-solutions)<br />* [https://www.elastic.co/products/shield](https://www.elastic.co/products/shield)</p>
    <p>El plazo m&aacute;ximo que otorga el Departamento de Seguridad L&oacute;gica ante esta incidencia es 5 d&iacute;as laborables, posterior a &eacute;ste, y si no se ha recibido respuesta a la presente notificaci&oacute;n de su parte, se proceder&aacute; al bloqueo de recepci&oacute;n de tr&aacute;fico por la IP y puerto ofensor (TCP 9200 y 9300).</p>
    <p>Quedamos a la espera de su pronta respuesta, y agradecemos la atenci&oacute;n prestada a la presente.</p>
    <p>Para una oportuna gesti&oacute;n del incidente, favor dirigir sus respuestas al correo <strong>&lt;soporte_seguridad@telconet.ec&gt;</strong></p>
    <p>Atentamente,</p>
    <p>Soporte T&eacute;cnico <br />Telconet S.A</p>
  </body>
</html>'),
'Activo',SYSDATE,'nnaulal',null,null,null);

SET DEFINE OFF 
INSERT INTO DB_COMUNICACION.ADMI_PLANTILLA (ID_PLANTILLA, NOMBRE_PLANTILLA, CODIGO, MODULO, PLANTILLA, ESTADO, FE_CREACION, USR_CREACION, FE_ULT_MOD, USR_ULT_MOD, EMPRESA_COD)
VALUES (DB_COMUNICACION.SEQ_ADMI_PLANTILLA.NEXTVAL,'NOTIFICACIÓN_OPEN_IPMI','IPMI','SOPORTE',
TO_CLOB('<html>
  <head>
  </head>
  <body>
    <p><strong>NOTIFICACI&Oacute;N OPEN IPMI</strong><br />======================</p>
    <p>Estimado cliente,</p>
    <p style="text-align: justify;">En reporte enviado a Telconet por ARCOTEL (Agencia de Regulaci&oacute;n y Control de las Telecomunicaciones), a trav&eacute;s de su departamento EcuCERT, le informamos que la IP<strong> {{ip}}</strong> asignada a <strong>{{login}}</strong> tiene una <strong>interfaz IPMI accesible desde el internet a trav&eacute;s del puerto UDP 623</strong>. Este tipo de interfaces permiten a los administradores de sistemas realizar operaciones sobre el servidor directamente a trav&eacute;s de las interfaces de red, proveyendo acceso al BIOS, disco duro y dem&aacute;s hardware. Un atacante puede utilizar IPMI para obtener acceso a nivel f&iacute;sico del servidor, reiniciar el sistema, instalar un nuevo sistema operativo, comprometer datos, entre otros, sin necesidad de pasar por ning&uacute;n control del sistema operativo.</p>
    <p style="text-align: justify;">Telconet, con el fin de precautelar la disponibilidad e integridad de su servicio, el de nuestros clientes y el de nuestra infraestructura, solicita su colaboraci&oacute;n realizando las siguientes acciones:</p>
    ') 
    || TO_CLOB('
	<p>* Restringir el acceso a la interfaz IPMI &uacute;nicamente a direcciones IP de la red de administraci&oacute;n (de preferencia desde la red interna).<br />* Restringir el tr&aacute;fico IPMI (puerto UDP 623) a VLANs de administraci&oacute;n &uacute;nicamente.<br />* Utilizar contrase&ntilde;as seguras.<br />* Habilitar el cifrado de tr&aacute;fico de ser posible (consultar manual del fabricante).<br />* Requerir autenticaci&oacute;n.<br />* Desactivar "cifrado 0" que es vulnerable, y los inicios de sesi&oacute;n an&oacute;nimos. "cifrado 0" es<br />una opci&oacute;n habilitada por defecto en muchos dispositivos habilitados para IPMI.</p>
    <p style="text-align: justify;">En caso de no tener este tipo de interfaces habilitadas, o que este servicio se encuentre habilitado sin su conocimiento, considerar que el mismo podr&iacute;a encontrarse comprometido; de ser as&iacute;, una revisi&oacute;n m&aacute;s minuciosa debe ser realizada para determinar las causas de la incidencia. Si existe una red interna nateada a trav&eacute;s de la IP ofensora, todos los equipos de esta red deben ser revisados, con el fin de localizar al equipo que presente el servicio activo.</p>
    <p>M&aacute;s informaci&oacute;n sobre la vulnerabilidad en el servicio IPMI y c&oacute;mo configurar de manera segura los dispositivos afectados puede ser encontrada en el sitio web de EcuCERT</p>
    ')
    || TO_CLOB('
    <p>* [http://www.ecucert.gob.ec/open_ipmi.html#ANCHOR_Box1](http://www.ecucert.gob.ec/open_ipmi.html#ANCHOR_Box1)</p>
    <p>El plazo m&aacute;ximo que otorga el Departamento de Seguridad L&oacute;gica ante esta incidencia es <strong>5 d&iacute;as laborables</strong>, posterior a &eacute;ste, y si no se ha recibido respuesta a la presente notificaci&oacute;n de su parte, se proceder&aacute; al bloqueo de recepci&oacute;n de tr&aacute;fico por la IP y puerto ofensor (UDP 623).</p>
    <p>Quedamos a la espera de su pronta respuesta, y agradecemos la atenci&oacute;n prestada a la presente.</p>
    <p>Para una oportuna gesti&oacute;n del incidente, favor dirigir sus respuestas al correo <strong>&lt;soporte_seguridad@telconet.ec&gt;</strong></p>
    <p>Atentamente,</p>
    <p>Soporte T&eacute;cnico <br />Telconet S.A</p>
  </body>
</html>'),
'Activo',SYSDATE,'nnaulal',null,null,null);

SET DEFINE OFF 
INSERT INTO DB_COMUNICACION.ADMI_PLANTILLA (ID_PLANTILLA, NOMBRE_PLANTILLA, CODIGO, MODULO, PLANTILLA, ESTADO, FE_CREACION, USR_CREACION, FE_ULT_MOD, USR_ULT_MOD, EMPRESA_COD)
VALUES (DB_COMUNICACION.SEQ_ADMI_PLANTILLA.NEXTVAL,'NOTIFICACIÓN_MEMCACHED','MEMCACHED','SOPORTE',
TO_CLOB('<html>
  <head>
  </head>
  <body>
    <p><strong>NOTIFICACI&Oacute;N Memcached</strong><br />======================</p>
    <p>Estimado cliente,</p>
    <p style="text-align: justify;">En reporte enviado a Telconet por ARCOTEL (Agencia de Regulaci&oacute;n y Control de las Telecomunicaciones), a trav&eacute;s de su departamento EcuCERT, le informamos que la IP <strong>{{ip}} </strong>asignada a <strong>{{login}}</strong> presenta el servicio <strong>Memcached habilitado y accesible desde Internet por el puerto TCP 11211</strong>. Al encontrarse el servicio accesible, es posible recuperar informaci&oacute;n sobre el servidor y sobre el servicio, puesto es un servicio que no cuenta con ning&uacute;n tipo de autenticaci&oacute;n.</p>
    ') 
    || TO_CLOB('
    <p style="text-align: justify;">Telconet, con el fin de precautelar la disponibilidad e integridad de su servicio, el de nuestros clientes y el de nuestra infraestructura, solicita su colaboraci&oacute;n restringiendo las conexiones al<br />puerto TCP 11211 por IP de origen, s&oacute;lo a los servidores que necesiten acceder al servicio. Si el servicio no est&aacute; siendo utilizado, deshabilitarlo por completo.</p>
    <p style="text-align: justify;">En caso de que este servicio/puerto se encuentre habilitado sin su conocimiento, considerar que el servidor podr&iacute;a encontrarse comprometido; de ser as&iacute;, una revisi&oacute;n m&aacute;s minuciosa debe ser realizada para determinar las causas de la incidencia. Si existe una red interna nateada a trav&eacute;s de la IP ofensora, todos los equipos de esta red deben ser revisados, con el fin de localizar al equipo que presente el servicio activo.</p>
    <p>M&aacute;s informaci&oacute;n sobre la vulnerabilidad en el servicio Memcached y c&oacute;mo configurar de manera segura los dispositivos afectados puede ser encontrada en los siguientes enlaces:</p>
    ')
    || TO_CLOB('
    <p>* [http://niiconsulting.com/checkmate/2013/05/memcache-exploit/](http://niiconsulting.com/checkmate/2013/05/memcache-exploit/)<br />* [https://kb.iweb.com/entries/90993237-C%C3%B3mo-proteger-su-servidor-del-servicio-de-<br />Memcached](https://kb.iweb.com/entries/90993237-C%C3%B3mo-proteger-su-servidor-del-servicio-de-<br />Memcached)</p>
    <p>El plazo m&aacute;ximo que otorga el Departamento de Seguridad L&oacute;gica ante esta incidencia es <strong>5 d&iacute;as laborables</strong>, posterior a &eacute;ste, y si no se ha recibido respuesta a la presente notificaci&oacute;n de su parte, se proceder&aacute; al bloqueo de recepci&oacute;n de tr&aacute;fico por la IP y puerto ofensor (TCP 11211).</p>
    <p>Quedamos a la espera de su pronta respuesta, y agradecemos la atenci&oacute;n prestada a la presente.</p>
    <p>Para una oportuna gesti&oacute;n del incidente, favor dirigir sus respuestas al correo <strong>&lt;soporte_seguridad@telconet.ec&gt;</strong></p>
    <p>Atentamente,</p>
    <p>Soporte T&eacute;cnico <br />Telconet S.A</p>
  </body>
</html>'),
'Activo',SYSDATE,'nnaulal',null,null,null);

SET DEFINE OFF 
INSERT INTO DB_COMUNICACION.ADMI_PLANTILLA (ID_PLANTILLA, NOMBRE_PLANTILLA, CODIGO, MODULO, PLANTILLA, ESTADO, FE_CREACION, USR_CREACION, FE_ULT_MOD, USR_ULT_MOD, EMPRESA_COD)
VALUES (DB_COMUNICACION.SEQ_ADMI_PLANTILLA.NEXTVAL,'NOTIFICACIÓN_OPEN_MONGODB','MONGODB','SOPORTE',
TO_CLOB('<html>
  <head>
  </head>
  <body>
    <p><strong>NOTIFICACI&Oacute;N OPEN MongoDB</strong><br />=========================</p>
    <p>Estimado cliente,</p>
    <p style="text-align: justify;">En reporte enviado a Telconet por ARCOTEL (Agencia de Regulaci&oacute;n y Control de las Telecomunicaciones), a trav&eacute;s de su departamento EcuCERT, le informamos que la IP <strong>{{ip}} </strong>asignada a <strong>{{login}}</strong> presenta el servicio MongoDB habilitado y <strong>accesible desde </strong><strong>Internet por el puerto TCP 27017</strong>. Al encontrarse el servicio accesible, es posible recuperar informaci&oacute;n sobre el sistema operativo y las bases de datos configuradas por el motor de bases de datos. Si no se ha configurado correctamente el acceso autenticado al servicio, incluso es posible acceder sin restricciones a todas las bases de datos.</p>
    ') 
    || TO_CLOB('
    <p style="text-align: justify;">Telconet, con el fin de precautelar la disponibilidad e integridad de su servicio, el de nuestros clientes y el de nuestra infraestructura, solicita su colaboraci&oacute;n restringiendo las conexiones al<br />puerto TCP 27017 y configurando el acceso autenticado a las bases de datos, en caso de no encontrarse configurado. En caso de que el servicio no est&eacute; siendo utilizado, deshabilitarlo y bloquear completamente el acceso al puerto ofensor.</p>
    <p style="text-align: justify;">En caso de que este servicio/puerto se encuentre habilitado sin su conocimiento, considerar que el equipo podr&iacute;a encontrarse comprometido; de ser as&iacute;, una revisi&oacute;n m&aacute;s minuciosa debe ser realizada para determinar las causas de la incidencia. Si existe una red interna nateada a trav&eacute;s de la IP ofensora, todos los equipos de esta red deben ser revisados, con el fin de localizar al equipo que presente el servicio activo.</p>
    <p>M&aacute;s informaci&oacute;n sobre la vulnerabilidad en el servicio MongoDB y c&oacute;mo configurar de manera<br />segura los dispositivos afectados puede ser encontrada en los siguientes enlaces:</p>
    ')
    || TO_CLOB('
    <p>* [http://www.ecucert.gob.ec/mongodb.html#ANCHOR_Box1](http://www.ecucert.gob.ec/mongodb.html#ANCHOR_Box1)<br />* [http://docs.mongodb.org/manual/core/security-introduction/#authentication](http://docs.mongodb.org/manual/core/security-introduction/#authentication)</p>
    <p style="text-align: justify;">El plazo m&aacute;ximo que otorga el Departamento de Seguridad L&oacute;gica ante esta incidencia es <strong>5 d&iacute;as </strong><strong>laborables</strong>, posterior a &eacute;ste, y si no se ha recibido respuesta a la presente notificaci&oacute;n de su parte, se proceder&aacute; al bloqueo de recepci&oacute;n de tr&aacute;fico por la IP y puerto ofensor (TCP 27017).</p>
    <p>Quedamos a la espera de su pronta respuesta, y agradecemos la atenci&oacute;n prestada a la presente.</p>
    <p>Para una oportuna gesti&oacute;n del incidente, favor dirigir sus respuestas al correo <strong>&lt;soporte_seguridad@telconet.ec&gt;</strong></p>
    <p>Atentamente,</p>
    <p>Soporte T&eacute;cnico <br />Telconet S.A</p>
  </body>
</html>'),
'Activo',SYSDATE,'nnaulal',null,null,null);

SET DEFINE OFF 
INSERT INTO DB_COMUNICACION.ADMI_PLANTILLA (ID_PLANTILLA, NOMBRE_PLANTILLA, CODIGO, MODULO, PLANTILLA, ESTADO, FE_CREACION, USR_CREACION, FE_ULT_MOD, USR_ULT_MOD, EMPRESA_COD)
VALUES (DB_COMUNICACION.SEQ_ADMI_PLANTILLA.NEXTVAL,'NOTIFICACIÓN_OPEN_MS-SQL','MSSQL','SOPORTE',
TO_CLOB('<html>
  <head>
  </head>
  <body>
    <p>&nbsp;</p>
    <p><strong>NOTIFICACI&Oacute;N OPEN MS-SQL</strong><br />========================</p>
    <p>Estimado cliente,</p>
    <p style="text-align: justify;">En reporte enviado a Telconet por ARCOTEL (Agencia de Regulaci&oacute;n y Control de las Telecomunicaciones), a trav&eacute;s de su departamento EcuCERT, le informamos que la IP <strong>{{ip}} </strong>asignada a <strong>{{login}}</strong> presenta el servicio <strong>MS-SQL Server Browser Service habilitado y accesible desde Internet por el puerto UDP 1434</strong>. Al encontrarse el servicio accesible, es posible recuperar informaci&oacute;n sobre el sistema operativo y las bases de datos/instancias configuradas por el motor de bases de datos.</p>
    ') 
    || TO_CLOB('
    <p style="text-align: justify;">Telconet, con el fin de precautelar la disponibilidad e integridad de su servicio, el de nuestros clientes y el de nuestra infraestructura, solicita su colaboraci&oacute;n restringiendo las conexiones al<br />puerto UDP 1434 y configurando el acceso autenticado a las bases de datos, en caso de no encontrarse configurado. En caso de que el servicio no est&eacute; siendo utilizado, deshabilitarlo y bloquear completamente el acceso al puerto ofensor.</p>
    <p style="text-align: justify;">En caso de que este servicio/puerto se encuentre habilitado sin su conocimiento, considerar que el equipo podr&iacute;a encontrarse comprometido; de ser as&iacute;, una revisi&oacute;n m&aacute;s minuciosa debe ser realizada para determinar las causas de la incidencia. Si existe una red interna nateada a trav&eacute;s de la IP ofensora, todos los equipos de esta red deben ser revisados, con el fin de localizar al equipo que presente el servicio activo.</p>
    <p>M&aacute;s informaci&oacute;n la vulnerabilidad en el servicio MS-SQL y c&oacute;mo configurar de manera segura los dispositivos afectados puede ser encontrada en los siguientes enlaces:</p>
    ')
    || TO_CLOB('
    <p>* [https://technet.microsoft.com/es-es/library/ms175483%28v=sql.105%29.aspx](https://technet.microsoft.com/es-es/library/ms175483%28v=sql.105%29.aspx)<br />* [https://technet.microsoft.com/en-us/library/ms181087%28v=sql.105%29.aspx](https://technet.microsoft.com/en-us/library/ms181087%28v=sql.105%29.aspx)</p>
    <p style="text-align: justify;">El plazo m&aacute;ximo que otorga el Departamento de Seguridad L&oacute;gica ante esta incidencia es <strong>5 d&iacute;as </strong><strong>laborables</strong>, posterior a &eacute;ste, y si no se ha recibido respuesta a la presente notificaci&oacute;n de su parte, se proceder&aacute; al bloqueo de recepci&oacute;n de tr&aacute;fico por la IP y puerto ofensor (UDP 1434).</p>
    <p>Quedamos a la espera de su pronta respuesta, y agradecemos la atenci&oacute;n prestada a la presente.</p>
    <p>Para una oportuna gesti&oacute;n del incidente, favor dirigir sus respuestas al correo <strong>&lt;soporte_seguridad@telconet.ec&gt;</strong></p>
    <p>Atentamente,</p>
    <p>Soporte T&eacute;cnico <br />Telconet S.A</p>
  </body>
</html>'),
'Activo',SYSDATE,'nnaulal',null,null,null);

SET DEFINE OFF 
INSERT INTO DB_COMUNICACION.ADMI_PLANTILLA (ID_PLANTILLA, NOMBRE_PLANTILLA, CODIGO, MODULO, PLANTILLA, ESTADO, FE_CREACION, USR_CREACION, FE_ULT_MOD, USR_ULT_MOD, EMPRESA_COD)
VALUES (DB_COMUNICACION.SEQ_ADMI_PLANTILLA.NEXTVAL,'NOTIFICACIÓN_NAT-PMP','NATPMP','SOPORTE',
TO_CLOB('<html>
  <head>
  </head>
  <body>
    <p>&nbsp;</p>
    <p><strong>NOTIFICACI&Oacute;N NAT-PMP</strong><br />====================</p>
    <p>Estimado cliente,</p>
    <p style="text-align: justify;">En reporte enviado a Telconet por ARCOTEL (Agencia de Regulaci&oacute;n y Control de las Telecomunicaciones), a trav&eacute;s de su departamento EcuCERT, le informamos que la IP <strong>{{ip}} </strong>asignada a <strong>{{login}}</strong> presenta el servicio <strong>NAT-PMP habilitado y accesible desde Internet por el puerto UDP 5351</strong>. NAT-PMP es un protocolo de asignaci&oacute;n de puertos, donde un NAT es solicitado por un host de la red local de confianza para reenviar el tr&aacute;fico entre la red externa y el anfitri&oacute;n de petici&oacute;n. Si es configurado de forma incorrecta, puede aceptar solicitudes de asignaci&oacute;n de puertos malicioso o revelar informaci&oacute;n sobre s&iacute; mismo.</p>
    ') 
    || TO_CLOB('
    <p style="text-align: justify;">Telconet, con el fin de precautelar la disponibilidad e integridad de su servicio, el de nuestros clientes y el de nuestra infraestructura, solicita su colaboraci&oacute;n configurando de forma segura los dispositivos que hagan uso del servicio NAT-PMP, espec&iacute;ficamente, que las interfaces LAN y WAN est&aacute;n bien asignadas. Peticiones NAT-PMP deben ser aceptadas &uacute;nicamente por las interfaces internas, y asignaciones de puerto deben ser abiertas &uacute;nicamente para la direcci&oacute;n IP interna solicitante. En caso de que el servicio no sea utilizado, bloquear las conexiones UDP al puerto 5351.</p>
    <p style="text-align: justify;">En caso de que este servicio/puerto se encuentre habilitado sin su conocimiento, considerar que el equipo podr&iacute;a encontrarse comprometido; de ser as&iacute;, una revisi&oacute;n m&aacute;s minuciosa debe ser realizada para determinar las causas de la incidencia. Si existe una red interna nateada a trav&eacute;s de la IP ofensora, todos los equipos de esta red deben ser revisados, con el fin de localizar al equipo que presente el servicio activo.</p>
    <p>M&aacute;s informaci&oacute;n sobre la vulnerabilidad en el servicio NAT-PMP y c&oacute;mo configurar de manera<br />segura los dispositivos afectados puede ser encontrada en el sitio web de EcuCERT</p>
    ')
    || TO_CLOB('
    <p>* [http://www.ecucert.gob.ec/nat-pmp.html#ANCHOR_Box1](http://www.ecucert.gob.ec/nat-pmp.html#ANCHOR_Box1)</p>
    <p style="text-align: justify;">El plazo m&aacute;ximo que otorga el Departamento de Seguridad L&oacute;gica ante esta incidencia es <strong>5 d&iacute;as laborables</strong>, posterior a &eacute;ste, y si no se ha recibido respuesta a la presente notificaci&oacute;n de su parte, se proceder&aacute; al bloqueo de recepci&oacute;n de tr&aacute;fico por la IP y puerto ofensor (UDP 5351).</p>
    <p>Quedamos a la espera de su pronta respuesta, y agradecemos la atenci&oacute;n prestada a la presente.</p>
    <p>Para una oportuna gesti&oacute;n del incidente, favor dirigir sus respuestas al correo <strong>&lt;soporte_seguridad@telconet.ec&gt;</strong></p>
    <p>Atentamente,</p>
    <p>Soporte T&eacute;cnico <br />Telconet S.A</p>
  </body>
</html>'),
'Activo',SYSDATE,'nnaulal',null,null,null);

SET DEFINE OFF 
INSERT INTO DB_COMUNICACION.ADMI_PLANTILLA (ID_PLANTILLA, NOMBRE_PLANTILLA, CODIGO, MODULO, PLANTILLA, ESTADO, FE_CREACION, USR_CREACION, FE_ULT_MOD, USR_ULT_MOD, EMPRESA_COD)
VALUES (DB_COMUNICACION.SEQ_ADMI_PLANTILLA.NEXTVAL,'NOTIFICACIÓN_OPEN_NETBIOS','NETBIOS','SOPORTE',
TO_CLOB('<html>
  <head>
  </head>
  <body>
    <p>&nbsp;</p>
    <p><strong>NOTIFICACI&Oacute;N OPEN NetBIOS</strong><br />=========================</p>
    <p>Estimado cliente,</p>
    <p style="text-align: justify;">En reporte enviado a Telconet por ARCOTEL (Agencia de Regulaci&oacute;n y Control de las Telecomunicaciones), a trav&eacute;s de su departamento EcuCERT, le informamos que la IP <strong>{{ip}} </strong>asignada a <strong>{{login}}</strong> presenta el servicio <strong>NetBIOS en funcionamiento y </strong><strong>accesible desde Internet en el puerto UDP 137</strong>. La caracter&iacute;stica de resoluci&oacute;n de nombres de NetBIOS tiene el potencial de ser utilizada en ataques de amplificaci&oacute;n de Denegaci&oacute;n de Servicio (DoS) cuando se encuentran accesibles desde el internet.</p>
    ') 
    || TO_CLOB('
    <p style="text-align: justify;">Telconet, con el fin de precautelar la disponibilidad e integridad de su servicio, el de nuestros clientes y el de nuestra infraestructura, solicita su colaboraci&oacute;n realizando una de las siguientes acciones:</p>
    <p>* Si el equipo afectado es un servidor Microsoft ISA 2000, proceder con la actualizaci&oacute;n a<br />una soluci&oacute;n similar (Fin de vida del proyecto: Marzo 2011).<br />* Este servicio no debe encontrarse disponible desde el internet; el acceso debe ser<br />restringido s&oacute;lo a IPs de confianza.<br />* De no ser necesario, deshabilitar la resoluci&oacute;n de nombres de NetBIOS de la siguiente<br />manera:<br /> &gt;* En Panel de Control -&gt; Conexiones de Red -&gt; Grupo de red del equipo (Red de<br />&aacute;rea Local / Red Inal&aacute;mbrica).<br /> &gt;* En Propiedades -&gt; Protocolo TPC/IP -&gt; Propiedades -&gt; Opciones avanzadas.<br /> &gt;* En la pesta&ntilde;a WINS deshabilitar NetBIOS.</p>
    <p><br />Si existe una red interna nateada a trav&eacute;s de la IP ofensora, todos los equipos de esta red deben ser revisados, con el fin de localizar al equipo que presente el servicio activo.</p>
    <p>M&aacute;s informaci&oacute;n sobre la vulnerabilidad OPEN NetBIOS y c&oacute;mo configurar de manera segura los dispositivos afectados puede ser encontrada en el sitio web de EcuCERT</p>
    ')
    || TO_CLOB('
    <p>* [http://www.ecucert.gob.ec/netbios.html#ANCHOR_Box1](http://www.ecucert.gob.ec/netbios.html#ANCHOR_Box1)</p>
    <p style="text-align: justify;">El plazo m&aacute;ximo que otorga el Departamento de Seguridad L&oacute;gica ante esta incidencia es <strong>5 d&iacute;as laborables</strong>, posterior a &eacute;ste, y si no se ha recibido respuesta a la presente notificaci&oacute;n de su parte, se proceder&aacute; al bloqueo de recepci&oacute;n de tr&aacute;fico por la IP y puerto ofensor (UDP 137-138 y TCP 139).</p>
    <p>Quedamos a la espera de su pronta respuesta, y agradecemos la atenci&oacute;n prestada a la presente.</p>
    <p>Para una oportuna gesti&oacute;n del incidente, favor dirigir sus respuestas al correo <strong>&lt;soporte_seguridad@telconet.ec&gt;</strong></p>
    <p>Atentamente,</p>
    <p>Soporte T&eacute;cnico <br />Telconet S.A</p>
  </body>
</html>'),
'Activo',SYSDATE,'nnaulal',null,null,null);

SET DEFINE OFF 
INSERT INTO DB_COMUNICACION.ADMI_PLANTILLA (ID_PLANTILLA, NOMBRE_PLANTILLA, CODIGO, MODULO, PLANTILLA, ESTADO, FE_CREACION, USR_CREACION, FE_ULT_MOD, USR_ULT_MOD, EMPRESA_COD)
VALUES (DB_COMUNICACION.SEQ_ADMI_PLANTILLA.NEXTVAL,'NOTIFICACIÓN_NTP_MONITOR','NTPMON','SOPORTE',
TO_CLOB('<html>
  <head>
  </head>
  <body>
    <p>&nbsp;</p>
    <p>NOTIFICACI&Oacute;N NTP MONITOR<br />========================</p>
    <p style="text-align: justify;">Estimado cliente,<br />En reporte enviado a Telconet por ARCOTEL (Agencia de Regulaci&oacute;n y Control de las Telecomunicaciones), a trav&eacute;s de su departamento EcuCERT, le informamos que la IP&nbsp; <strong>{{ip}} </strong>asignada a <strong>{{login}}</strong> tiene <strong>activada una opci&oacute;n en el servicio NTP que puede ser potencialmente utilizada para ataques de amplificaci&oacute;n en ataques de Denegaci&oacute;n de Servicio</strong> (DoS), basado en la utilizaci&oacute;n de servidores NTP p&uacute;blicamente accesibles.</p>
    <p>La comprobaci&oacute;n se puede realizar en una m&aacute;quina con Linux usando el siguiente comando: <br /><strong>ntpdc -n -c monlist {{ip}}</strong> ; si se recibe respuesta, el servidor es vulnerable.</p>
    ') 
    || TO_CLOB('
    <p style="text-align: justify;">Telconet, con el fin de precautelar la disponibilidad e integridad de su servicio, el de nuestros clientes y el de nuestra infraestructura, solicita su colaboraci&oacute;n para realizar los controles necesarios actualizando el servidor NTP a su versi&oacute;n m&aacute;s actual. De no ser posible realizar la actualizaci&oacute;n de manera inmediata, incluir en el archivo de configuraci&oacute;n del servidor las l&iacute;neas que se muestran a continuaci&oacute;n, hasta que se pueda realizar la actualizaci&oacute;n:</p>
    <p>`disable monitor <br /> restrict default noquery`<br />&oacute; <br />`disable monitor <br />restrict localhost` (esto &uacute;ltimo para permitir el uso de monitorizaci&oacute;n s&oacute;lo a redes internas, de ser necesario)</p>
    <p style="text-align: justify;">En caso de que este servicio se encuentre habilitado sin su conocimiento, considerar que el dispositivo podr&iacute;a encontrarse comprometido; de ser as&iacute;, una revisi&oacute;n m&aacute;s minuciosa debe ser realizada para determinar las causas de la incidencia. Si existe una red interna nateada a trav&eacute;s de la IP ofensora, todos los equipos de esta red deben ser revisados, con el fin de localizar al equipo que presente el servicio activo.</p>
    <p>M&aacute;s informaci&oacute;n sobre la vulnerabilidad NTP MONITOR y c&oacute;mo configurar de manera segura los dispositivos afectados puede ser encontrada en los siguientes enlaces:</p>
    ')
    || TO_CLOB('
    <p>* [http://www.ecucert.gob.ec/ntp_monitor.html#ANCHOR_Box1](http://www.ecucert.gob.ec/ntp_monitor.html#ANCHOR_Box1)<br />* [http://www.team-cymru.org/secure-ntp-template.html](http://www.team-cymru.org/secure-ntp-template.html)<br />* [http://support.ntp.org/bin/view/Support/AccessRestrictions](http://support.ntp.org/bin/view/Support/AccessRestrictions)</p>
    <p>El plazo m&aacute;ximo que otorga el Departamento de Seguridad L&oacute;gica ante esta incidencia es <strong>5 d&iacute;as laborables</strong>, posterior a &eacute;ste, y si no se ha recibido respuesta a la presente notificaci&oacute;n de su parte, se proceder&aacute; al bloqueo de recepci&oacute;n de tr&aacute;fico por la IP y puerto ofensor (UDP 123).</p>
    <p>Quedamos a la espera de su pronta respuesta, y agradecemos la atenci&oacute;n prestada a la presente.</p>
    <p>Para una oportuna gesti&oacute;n del incidente, favor dirigir sus respuestas al correo <strong>&lt;soporte_seguridad@telconet.ec&gt;</strong></p>
    <p>Atentamente,</p>
    <p>Soporte T&eacute;cnico <br />Telconet S.A</p>
  </body>
</html>'),
'Activo',SYSDATE,'nnaulal',null,null,null);

SET DEFINE OFF 
INSERT INTO DB_COMUNICACION.ADMI_PLANTILLA (ID_PLANTILLA, NOMBRE_PLANTILLA, CODIGO, MODULO, PLANTILLA, ESTADO, FE_CREACION, USR_CREACION, FE_ULT_MOD, USR_ULT_MOD, EMPRESA_COD)
VALUES (DB_COMUNICACION.SEQ_ADMI_PLANTILLA.NEXTVAL,'NOTIFICACIÓN_NTP_VERSION','NTPVERS','SOPORTE',
TO_CLOB('<html>
  <head>
  </head>
  <body>
    <p><strong>NOTIFICACI&Oacute;N NTP VERSION</strong><br />========================</p>
    <p style="text-align: justify;">Estimado cliente,<br />En reporte enviado a Telconet por ARCOTEL (Agencia de Regulaci&oacute;n y Control de las Telecomunicaciones), a trav&eacute;s de su departamento EcuCERT, le informamos que la IP <strong>{{ip}} </strong>asignada a <strong>{{login}}</strong> tiene <strong>activada una opci&oacute;n en el servicio NTP que puede ser potencialmente utilizada para ataques de amplificaci&oacute;n en ataques Distribuidos de Denegaci&oacute;n de Servicio</strong> (DDoS), basado en la utilizaci&oacute;n de servidores NTP p&uacute;blicamente accesibles.</p>
    <p style="text-align: justify;">La comprobaci&oacute;n se puede realizar en una m&aacute;quina con Linux usando el siguiente comando:&nbsp; <strong>ntpq -c rv {{ip}}</strong> ; si se recibe respuesta, el servidor es vulnerable.</p>
    <p style="text-align: justify;">Telconet, con el fin de precautelar la disponibilidad e integridad de su servicio, el de nuestros clientes y el de nuestra infraestructura, solicita su colaboraci&oacute;n para realizar los controles necesarios actualizando el servidor NTP a su versi&oacute;n m&aacute;s actual. De no ser posible realizar la actualizaci&oacute;n de manera inmediata, incluir en el archivo de configuraci&oacute;n del servidor las l&iacute;neas que se muestran a continuaci&oacute;n, hasta que se pueda realizar la actualizaci&oacute;n:</p>
    <p>`disable monitor<br />restrict default noquery`<br />&oacute; <br />`disable monitor<br />restrict localhost` (esto &uacute;ltimo para permitir el uso de monitorizaci&oacute;n s&oacute;lo a redes internas, de ser necesario)</p>
    <p style="text-align: justify;">En caso de que este servicio se encuentre habilitado sin su conocimiento, considerar que el dispositivo podr&iacute;a encontrarse comprometido; de ser as&iacute;, una revisi&oacute;n m&aacute;s minuciosa debe ser realizada para determinar las causas de la incidencia. Si existe una red interna nateada a trav&eacute;s de la IP ofensora, todos los equipos de esta red deben ser revisados, con el fin de localizar al equipo que presente el servicio activo.</p>
    ') || TO_CLOB('
	<p style="text-align: justify;">M&aacute;s informaci&oacute;n sobre la vulnerabilidad NTP VERSION y c&oacute;mo configurar de manera segura los dispositivos afectados, puede ser encontrada en los siguientes enlaces:</p>
    <p>* [http://www.ecucert.gob.ec/ntp_version.html#ANCHOR_Box1](http://www.ecucert.gob.ec/ntp_version.html#ANCHOR_Box1)<br />* [http://www.team-cymru.org/secure-ntp-template.html](http://www.team-cymru.org/secure-ntp-template.html)<br />* [http://support.ntp.org/bin/view/Support/AccessRestrictions](http://support.ntp.org/bin/view/Support/AccessRestrictions)</p>
    <p style="text-align: justify;">El plazo m&aacute;ximo que otorga el Departamento de Seguridad L&oacute;gica ante esta incidencia es <strong>5 d&iacute;as laborables</strong>, posterior a &eacute;ste, y si no se ha recibido respuesta a la presente notificaci&oacute;n de su parte, se proceder&aacute; al bloqueo de recepci&oacute;n de tr&aacute;fico por la IP y puerto ofensor (UDP 123).</p>
    <p>Quedamos a la espera de su pronta respuesta, y agradecemos la atenci&oacute;n prestada a la presente.</p>
    <p>Para una oportuna gesti&oacute;n del incidente, favor dirigir sus respuestas al correo <strong>&lt;soporte_seguridad@telconet.ec&gt;</strong></p>
    <p>Atentamente, <br />Soporte T&eacute;cnico <br />Telconet S.A.</p>   
  </body>
</html>'),
'Activo',SYSDATE,'nnaulal',null,null,null);

SET DEFINE OFF 
INSERT INTO DB_COMUNICACION.ADMI_PLANTILLA (ID_PLANTILLA, NOMBRE_PLANTILLA, CODIGO, MODULO, PLANTILLA, ESTADO, FE_CREACION, USR_CREACION, FE_ULT_MOD, USR_ULT_MOD, EMPRESA_COD)
VALUES (DB_COMUNICACION.SEQ_ADMI_PLANTILLA.NEXTVAL,'NOTIFICACIÓN_OPEN_CWMP','OPENCWMP','SOPORTE',
TO_CLOB('<html>
  <head>
  </head>
  <body>
    <p>&nbsp;</p>
    <p><strong>NOTIFICACI&Oacute;N CWMP Accesible</strong><br />==============================</p>
    <p>Estimado cliente,</p>
    <p style="text-align: justify;">En reporte enviado a Telconet por ARCOTEL (Agencia de Regulaci&oacute;n y Control de las Telecomunicaciones), a trav&eacute;s de su departamento EcuCERT, le informamos que la IP <strong>{{ip}}</strong> asignada a <strong>{{login}}</strong> presenta el servicio <strong>CWMP (CPE WAN Management Protocol) habilitado y accesible desde Internet</strong>. CWMP es un protocolo usado para la administraci&oacute;n remota de dispositivos finales. Este protocolo es ampliamente usado por ISPs para la comunicaci&oacute;n entre equipos de clientes (Customer-premises Equipment - CPE) y servidores de autoconfiguraci&oacute;n (Auto Configuration Servers - ACS).</p>
    <p style="text-align: justify;">Debido a las vulnerabilidades encontradas en el protocolo CWMP, los equipos que tienen el servicio accesible son vulnerables ya que un atacante podr&iacute;a tomar el control de los mismos y usarlos para realizar actividades il&iacute;citas.</p>
    ') 
    || TO_CLOB('
    <p>Telconet, con el fin de precautelar la disponibilidad e integridad de su servicio, el de nuestros<br />clientes y el de nuestra infraestructura, solicita su colaboraci&oacute;n:</p>
    <p>* Deshabilitando el servicio CWMP, o bloquear el puerto TCP 7547, en caso de no ser utilizado. <br />* Si el servicio es necesario, restringir las conexiones TCP al puerto 7547 &uacute;nicamente desde IPs de confianza.</p>
    <p style="text-align: justify;">Adicionalmente, en caso de que este servicio/puerto se encuentre habilitado sin su conocimiento, considerar que el equipo podr&iacute;a encontrarse comprometido; de ser as&iacute;, una revisi&oacute;n m&aacute;s minuciosa debe ser realizada para determinar las causas de la incidencia. Si existe una red interna nateada a trav&eacute;s de la IP ofensora, todos los equipos de esta red deben ser revisados, con el fin de localizar al equipo que presente el servicio activo.</p>
    <p style="text-align: justify;">M&aacute;s informaci&oacute;n sobre la vulnerabilidad CWMP accesible puede ser encontrada en el siguiente enlace:</p>
    <p>* [https://cwmpscan.shadowserver.org/](https://cwmpscan.shadowserver.org/)<br />* [http://www.pcworld.com/article/2861232/vulnerability-in-embedded-web-server-exposes-millions-of-routers-to-hacking.html](http://www.pcworld.com/article/2861232/vulnerability-in-embedded-web-server-exposes-millions-of-routers-to-hacking.html)</p>
    ')
    || TO_CLOB('
    <p style="text-align: justify;">El plazo m&aacute;ximo que otorga el Departamento de Seguridad L&oacute;gica ante esta incidencia es <strong>5 d&iacute;as laborables</strong>, posterior a &eacute;ste, y si no se ha recibido respuesta a la presente notificaci&oacute;n de su parte, se proceder&aacute; al bloqueo de recepci&oacute;n de tr&aacute;fico por la IP y puerto ofensor (TCP 7547).</p>
    <p>Quedamos a la espera de su pronta respuesta, y agradecemos la atenci&oacute;n prestada a la presente.</p>
    <p>Para una oportuna gesti&oacute;n del incidente, favor dirigir sus respuestas al correo <strong>&lt;soporte_seguridad@telconet.ec&gt;</strong></p>
    <p>Atentamente,</p>
    <p>Soporte T&eacute;cnico <br />Telconet S.A</p>
  </body>
</html>'),
'Activo',SYSDATE,'nnaulal',null,null,null);

SET DEFINE OFF 
INSERT INTO DB_COMUNICACION.ADMI_PLANTILLA (ID_PLANTILLA, NOMBRE_PLANTILLA, CODIGO, MODULO, PLANTILLA, ESTADO, FE_CREACION, USR_CREACION, FE_ULT_MOD, USR_ULT_MOD, EMPRESA_COD)
VALUES (DB_COMUNICACION.SEQ_ADMI_PLANTILLA.NEXTVAL,'NOTIFICACIÓN_OPEN_DNS_RESOLVER','OPENDNS','SOPORTE',
TO_CLOB('<html>
  <head>
  </head>
  <body>
    <p>&nbsp;</p>
    <p><strong>NOTIFICACI&Oacute;N Open DNS Resolver</strong><br />==============================</p>
    <p>Estimado cliente,</p>
    <p style="text-align: justify;">En reporte enviado a Telconet por ARCOTEL (Agencia de Regulaci&oacute;n y Control de las Telecomunicaciones), a trav&eacute;s de su departamento EcuCERT, le informamos que la IP <strong>{{ip}} </strong>asignada a <strong>{{login}}</strong> <strong>se encuentra respondiendo a consultas recursivas de DNS</strong>, realizadas por hosts o direcciones IP que no corresponden al dominio de este servidor DNS. Esta condici&oacute;n puede ser abusada por un posible atacante que realice un gran n&uacute;mero de consultas al servidor, lo que provocar&iacute;a una situaci&oacute;n de denegaci&oacute;n de servicio (DoS), al consumir todos los recursos del servidor intentando responder a estas consultas. Un servidor con esta mala configuraci&oacute;n puede ser tambi&eacute;n utilizado en ataques de amplificaci&oacute;n DDoS para afectar a terceras partes.</p>
    <p style="text-align: justify;">Telconet, con el fin de precautelar la disponibilidad e integridad de su servicio, el de nuestros clientes y el de nuestra infraestructura, solicita su colaboraci&oacute;n configurando adecuadamente el servidor DNS:</p>
    ') 
    || TO_CLOB('
    <p>* Si el servicio no est&aacute; siendo utilizado, deshabilitarlo por completo y bloquear el acceso al<br />puerto UDP 53.<br />* Si el servicio est&aacute; siendo utilizado, revisar e implementar configuraciones seguras para el<br />mismo.</p>
    <p style="text-align: justify;">En caso de que este servicio/puerto se encuentre habilitado sin su conocimiento, considerar que el servidor podr&iacute;a encontrarse comprometido; de ser as&iacute;, una revisi&oacute;n m&aacute;s minuciosa debe ser realizada para determinar las causas de la incidencia. Si existe una red interna nateada a trav&eacute;s de la IP ofensora, todos los equipos de esta red deben ser revisados, con el fin de localizar al equipo que presente el servicio activo.</p>
    <p style="text-align: justify;">M&aacute;s informaci&oacute;n sobre la vulnerabilidad en el servicio DNS y c&oacute;mo configurar de manera segura los dispositivos afectados puede ser encontrada en los siguientes enlaces:</p>
    <p>* [http://www.ecucert.gob.ec/open_dns_resolver.html#ANCHOR_Box1](http://www.ecucert.gob.ec/open_dns_resolver.html#ANCHOR_Box1)<br />* [http://www.dnsstuff.com/docs/dnsreport](http://www.dnsstuff.com/docs/dnsreport)<br />* [http://openresolverproject.org/](http://openresolverproject.org/)<br />* [http://www.team-cymru.org/Open-Resolver-Challenge.html#instructions](http://www.team-cymru.org/Open-Resolver-Challenge.html#instructions)</p>
    ')
    || TO_CLOB('
    <p style="text-align: justify;">El plazo m&aacute;ximo que otorga el Departamento de Seguridad L&oacute;gica ante esta incidencia es <strong>5 d&iacute;as laborables</strong>, posterior a &eacute;ste, y si no se ha recibido respuesta a la presente notificaci&oacute;n de su parte, se proceder&aacute; al bloqueo de recepci&oacute;n de tr&aacute;fico por la IP y puerto ofensor (UDP 53).</p>
    <p style="text-align: justify;">Quedamos a la espera de su pronta respuesta, y agradecemos la atenci&oacute;n prestada a la presente.</p>
    <p>Para una oportuna gesti&oacute;n del incidente, favor dirigir sus respuestas al correo <strong>&lt;soporte_seguridad@telconet.ec&gt;</strong></p>
    <p>Atentamente,</p>
    <p>Soporte T&eacute;cnico <br />Telconet S.A</p>
  </body>
</html>'),
'Activo',SYSDATE,'nnaulal',null,null,null);

SET DEFINE OFF 
INSERT INTO DB_COMUNICACION.ADMI_PLANTILLA (ID_PLANTILLA, NOMBRE_PLANTILLA, CODIGO, MODULO, PLANTILLA, ESTADO, FE_CREACION, USR_CREACION, FE_ULT_MOD, USR_ULT_MOD, EMPRESA_COD)
VALUES (DB_COMUNICACION.SEQ_ADMI_PLANTILLA.NEXTVAL,'NOTIFICACIÓN_OPEN_MDNS','OPENMDNS','SOPORTE',
TO_CLOB('<html>
  <head>
  </head>
  <body>
    <p>&nbsp;</p>
    <p><strong>NOTIFICACI&Oacute;N mDNS Accesible</strong><br />==============================</p>
    <p>Estimado cliente,</p>
    <p style="text-align: justify;">En reporte enviado a Telconet por ARCOTEL (Agencia de Regulaci&oacute;n y Control de las Telecomunicaciones), a trav&eacute;s de su departamento EcuCERT, le informamos que la IP <strong>{{ip}} </strong>asignada a<strong> {{login}}</strong> presenta el servicio <strong>mDNS (Multicast Domain Name System) habilitado y accesible desde Internet</strong>. mDNS resuelve nombre de equipos a direcciones IPs en redes peque&ntilde;as que no tienen un servidor de nombres local.</p>
    <p style="text-align: justify;">Los equipos con mDNS accesible pueden ser usados en ataques de amplificaci&oacute;n UDP y adem&aacute;s pueden revelar mucha informaci&oacute;n acerca del sistema a atacantes.</p>
    ') 
    || TO_CLOB('
    <p style="text-align: justify;">Telconet, con el fin de precautelar la disponibilidad e integridad de su servicio, el de nuestros<br />clientes y el de nuestra infraestructura, solicita su colaboraci&oacute;n:</p>
    <p style="text-align: justify;">* Deshabilitando el servicio mDNS, o bloquear el puerto UDP 5353, en caso de no ser utilizado. <br />* Si el servicio es necesario, restringir las conexiones UDP al puerto 5353 &uacute;nicamente desde IPs de confianza.</p>
    <p style="text-align: justify;">Adicionalmente, en caso de que este servicio/puerto se encuentre habilitado sin su conocimiento, considerar que el equipo podr&iacute;a encontrarse comprometido; de ser as&iacute;, una revisi&oacute;n m&aacute;s minuciosa debe ser realizada para determinar las causas de la incidencia. Si existe una red interna nateada a trav&eacute;s de la IP ofensora, todos los equipos de esta red deben ser revisados, con el fin de localizar al equipo<br />que presente el servicio activo.</p>
    <p style="text-align: justify;">M&aacute;s informaci&oacute;n sobre la vulnerabilidad mDNS accesible puede ser encontrada en el siguiente enlace:</p>
    ')
    || TO_CLOB('
    <p>* [https://mdns.shadowserver.org/](https://mdns.shadowserver.org/)</p>
    <p>El plazo m&aacute;ximo que otorga el Departamento de Seguridad L&oacute;gica ante esta incidencia es **<strong>5 d&iacute;as laborables</strong>**, posterior a &eacute;ste, y si no se ha recibido respuesta a la presente notificaci&oacute;n de su parte, se proceder&aacute; al bloqueo de recepci&oacute;n de tr&aacute;fico por la IP y puerto ofensor (UDP 5353).</p>
    <p style="text-align: justify;">Quedamos a la espera de su pronta respuesta, y agradecemos la atenci&oacute;n prestada a la presente.</p>
    <p>Para una oportuna gesti&oacute;n del incidente, favor dirigir sus respuestas al correo <strong>&lt;soporte_seguridad@telconet.ec&gt;</strong></p>
    <p>Atentamente,</p>
    <p>Soporte T&eacute;cnico <br />Telconet S.A</p>
  </body>
</html>'),
'Activo',SYSDATE,'nnaulal',null,null,null);

SET DEFINE OFF 
INSERT INTO DB_COMUNICACION.ADMI_PLANTILLA (ID_PLANTILLA, NOMBRE_PLANTILLA, CODIGO, MODULO, PLANTILLA, ESTADO, FE_CREACION, USR_CREACION, FE_ULT_MOD, USR_ULT_MOD, EMPRESA_COD)
VALUES (DB_COMUNICACION.SEQ_ADMI_PLANTILLA.NEXTVAL,'NOTIFICACIÓN_PROXY_ABIERTO','OPENPROXY','SOPORTE',
TO_CLOB('<html>
  <head>
  </head>
  <body>
    <p>&nbsp;</p>
    <p><strong>NOTIFICACI&Oacute;N Proxy abierto</strong><br />==========================</p>
    <p>Estimado cliente,</p>
    <p style="text-align: justify;">En reporte enviado a Telconet por ARCOTEL (Agencia de Regulaci&oacute;n y Control de las Telecomunicaciones), a trav&eacute;s de su departamento EcuCERT, le informamos que la IP <strong>{{ip}} </strong>asignada a <strong>{{login}}</strong> est&aacute; prestando un servicio de <strong>proxy abierto por el puerto {{puerto}}</strong>. Un proxy es habitualmente utilizado para filtrar las conexiones/navegaci&oacute;n de los usuarios finales, de manera que se pueda controlar la seguridad de los mismos, controlar el ancho de banda, entre otros. Si el servicio se encuentra mal configurado, &eacute;ste podr&iacute;a permitir a cualquier usuario en el internet navegar a trav&eacute;s del proxy, situaci&oacute;n que es com&uacute;nmente utilizada para navegar de forma an&oacute;nima o saltarse controles basados en direccionamiento IP de determinados sitios. As&iacute; mismo, proxies abiertos son ampliamente utilizados para realizar actividades maliciosas en las redes, debido a que el servidor proxy oculta la direcci&oacute;n IP del atacante a los destinatarios.</p>
    <p style="text-align: justify;">La comprobaci&oacute;n se la puede realizar desde una m&aacute;quina con Linux, desde el internet, ejecutando el siguiente comando en el terminal, en una s&oacute;la l&iacute;nea:<br />&gt; http\_proxy=http://{ip}:{puerto}/ curl -4 &ndash;s http://nyc2.mirrors.digitalocean.com/tools/open_proxy_check.txt</p>
    ') 
    || TO_CLOB('
    <p style="text-align: justify;">Si el comando devuelve el texto <strong>&ldquo;If you are seeing this while running the open proxy text, your server is an open proxy&rdquo;</strong> significa que el servidor est&aacute; funcionando como un proxy abierto</p>
    <p style="text-align: justify;">Telconet, con el fin de precautelar la disponibilidad e integridad de su servicio, el de nuestros clientes y el de nuestra infraestructura, solicita su colaboraci&oacute;n configurando adecuadamente el servidor proxy. En caso de que este servicio/puerto se encuentre habilitado sin su conocimiento, considerar que el equipo podr&iacute;a encontrarse comprometido; de ser as&iacute;, una revisi&oacute;n m&aacute;s minuciosa debe ser realizada para determinar las causas de la incidencia. Si existe una red interna nateada a trav&eacute;s de la IP ofensora, todos los equipos de esta red deben ser revisados, con el fin de localizar al equipo que presente el servicio habilitado.</p>
    <p style="text-align: justify;">M&aacute;s informaci&oacute;n sobre la vulnerabilidad Proxy abierto y c&oacute;mo configurar de manera segura los dispositivos afectados puede ser encontrada en los siguientes enlaces:</p>
    ')
    || TO_CLOB('
    <p>* [http://httpd.apache.org/docs/trunk/mod/mod_proxy.html#access](http://httpd.apache.org/docs/trunk/mod/mod_proxy.html#access)<br />* [http://wiki.apache.org/httpd/ProxyAbuse](http://wiki.apache.org/httpd/ProxyAbuse)<br />* [http://www.squid-cache.org/Doc/config/acl/](http://www.squid-cache.org/Doc/config/acl/)<br />* [http://www.aboutonlinetips.com/what-is-an-open-proxy-server-and-how-to-close-a-proxy-server/](http://www.aboutonlinetips.com/what-is-an-open-proxy-server-and-how-to-close-a-proxy-server/)</p>
    <p>El plazo m&aacute;ximo que otorga el Departamento de Seguridad L&oacute;gica ante esta incidencia es <strong>5 d&iacute;as laborables</strong>, posterior a &eacute;ste, y si no se ha recibido respuesta a la presente notificaci&oacute;n de su parte, se proceder&aacute; al bloqueo de recepci&oacute;n de tr&aacute;fico por la IP y puerto ofensor.</p>
    <p style="text-align: justify;">Quedamos a la espera de su pronta respuesta, y agradecemos la atenci&oacute;n prestada a la presente.</p>
    <p>Para una oportuna gesti&oacute;n del incidente, favor dirigir sus respuestas al correo <strong>&lt;soporte_seguridad@telconet.ec&gt;</strong></p>
    <p>Atentamente,</p>
    <p>Soporte T&eacute;cnico <br />Telconet S.A</p>
  </body>
</html>'),
'Activo',SYSDATE,'nnaulal',null,null,null);

SET DEFINE OFF 
INSERT INTO DB_COMUNICACION.ADMI_PLANTILLA (ID_PLANTILLA, NOMBRE_PLANTILLA, CODIGO, MODULO, PLANTILLA, ESTADO, FE_CREACION, USR_CREACION, FE_ULT_MOD, USR_ULT_MOD, EMPRESA_COD)
VALUES (DB_COMUNICACION.SEQ_ADMI_PLANTILLA.NEXTVAL,'NOTIFICACIÓN_OPEN_RDP','OPENRDP','SOPORTE',
TO_CLOB('<html>
  <head>
  </head>
  <body>
    <p>&nbsp;</p>
    <p><strong>NOTIFICACI&Oacute;N RDP Accesible</strong><br />==============================</p>
    <p>Estimado cliente,</p>
    <p style="text-align: justify;">En reporte enviado a Telconet por ARCOTEL (Agencia de Regulaci&oacute;n y Control de las Telecomunicaciones), a trav&eacute;s de su departamento EcuCERT, le informamos que la IP <strong>{{ip}} </strong>asignada a <strong>{{login}}</strong> presenta el servicio <strong>RDP (Remote Desktop Protocol - Protocolo Escritorio Remoto) habilitado y accesible desde Internet</strong>. El protocolo Escritorio Remoto es un protocolo propietario desarrollado por Microsoft que provee a los usuarios interfaz gr&aacute;fica para conectarse a otra computadora sobre una conexi&oacute;n de red. Este servicio utiliza por defecto el puerto TCP 3389 en el servidor para recibir notificaciones.</p>
    <p style="text-align: justify;">La exposici&oacute;n de este servicio al internet es riesgosa debido a que los atacantes podr&iacute;an adivinar las credenciales por medio de fuerza bruta y acceder a informaci&oacute;n sensible alojada en el servidor remoto.<br />Adem&aacute;s, los atacantes podr&iacute;an explotar vulnerabilidades en la implementaci&oacute;n del protocolo en sistemas operativos no actualizados. Revisar el bolet&iacute;n de microsoft [aqu&iacute;](https://technet.microsoft.com/library/security/ms12-020).</p>
    ') 
    || TO_CLOB('
    <p style="text-align: justify;"><br />Telconet, con el fin de precautelar la disponibilidad e integridad de su servicio, el de nuestros<br />clientes y el de nuestra infraestructura, solicita su colaboraci&oacute;n:</p>
    <p>* Deshabilitando el servicio RDP, o bloquear el puerto TCP 3389, en caso de no ser utilizado. <br />* Si el servicio es necesario, restringir las conexiones TCP al puerto 3389 &uacute;nicamente desde IPs de confianza. <br />* Utilizar credenciales robustas para el acceso al servidor remoto.</p>
    <p style="text-align: justify;"><br />Adicionalmente, en caso de que este servicio/puerto se encuentre habilitado sin su conocimiento, considerar que el equipo podr&iacute;a encontrarse comprometido; de ser as&iacute;, una revisi&oacute;n m&aacute;s minuciosa debe ser realizada para determinar las causas de la incidencia. Si existe una red interna nateada a trav&eacute;s de la IP ofensora, todos los equipos de esta red deben ser revisados, con el fin de localizar al equipo que presente el servicio activo.</p>
    <p>M&aacute;s informaci&oacute;n sobre la vulnerabilidad open RDP (RDP accesible) puede ser encontrada los siguientes enlaces:</p>
    <p>* [https://rdpscan.shadowserver.org/](https://rdpscan.shadowserver.org/)<br />* [https://blogs.technet.microsoft.com/srd/2012/03/13/cve-2012-0002-a-closer-look-at-ms12-020s-critical-issue/](https://blogs.technet.microsoft.com/srd/2012/03/13/cve-2012-0002-a-closer-look-at-ms12-020s-critical-issue/)</p>
    ')
    || TO_CLOB('
    <p style="text-align: justify;">El plazo m&aacute;ximo que otorga el Departamento de Seguridad L&oacute;gica ante esta incidencia es <strong>5 d&iacute;as </strong><strong>laborables</strong>, posterior a &eacute;ste, y si no se ha recibido respuesta a la presente notificaci&oacute;n de su parte, se proceder&aacute; al bloqueo de recepci&oacute;n de tr&aacute;fico por la IP y puerto ofensor (TCP 3389).</p>
    <p style="text-align: justify;">Quedamos a la espera de su pronta respuesta, y agradecemos la atenci&oacute;n prestada a la presente.</p>
    <p>Para una oportuna gesti&oacute;n del incidente, favor dirigir sus respuestas al correo <strong>&lt;soporte_seguridad@telconet.ec&gt;</strong></p>
    <p>Atentamente,</p>
    <p>Soporte T&eacute;cnico <br />Telconet S.A</p>
  </body>
</html>'),
'Activo',SYSDATE,'nnaulal',null,null,null);

SET DEFINE OFF 
INSERT INTO DB_COMUNICACION.ADMI_PLANTILLA (ID_PLANTILLA, NOMBRE_PLANTILLA, CODIGO, MODULO, PLANTILLA, ESTADO, FE_CREACION, USR_CREACION, FE_ULT_MOD, USR_ULT_MOD, EMPRESA_COD)
VALUES (DB_COMUNICACION.SEQ_ADMI_PLANTILLA.NEXTVAL,'NOTIFICACIÓN_OPEN_SMB','OPENSMB','SOPORTE',
TO_CLOB('<html>
  <head>
  </head>
  <body>
    <p>&nbsp;</p>
    <p><strong>NOTIFICACI&Oacute;N SMB Accesible</strong><br />==============================</p>
    <p>Estimado cliente,</p>
    <p style="text-align: justify;">En reporte enviado a Telconet por ARCOTEL (Agencia de Regulaci&oacute;n y Control de las Telecomunicaciones), a trav&eacute;s de su departamento EcuCERT, le informamos que la IP <strong>{{ip}} </strong>asignada a <strong>{{login}}</strong> presenta el servicio <strong>SMB (Remote Desktop Protocol - Protocolo Escritorio Remoto) habilitado y accesible desde Internet</strong>. El protocolo Escritorio Remoto es un protocolo propietario desarrollado por Microsoft que provee a los usuarios interfaz gr&aacute;fica para conectarse a otra computadora sobre una conexi&oacute;n de red. Este servicio utiliza por defecto el puerto TCP 3389 en el servidor para recibir notificaciones.</p>
    <p style="text-align: justify;">La exposici&oacute;n de este servicio al internet es riesgosa debido a que los atacantes podr&iacute;an adivinar las credenciales por medio de fuerza bruta y acceder a informaci&oacute;n sensible alojada en el servidor remoto. Adem&aacute;s, los atacantes podr&iacute;an explotar vulnerabilidades en la implementaci&oacute;n del protocolo en sistemas operativos no actualizados. Revisar el bolet&iacute;n de microsoft [aqu&iacute;](https://technet.microsoft.com/library/security/ms12-020).</p>
    ') 
    || TO_CLOB('
    <p style="text-align: justify;"><br />Telconet, con el fin de precautelar la disponibilidad e integridad de su servicio, el de nuestros<br />clientes y el de nuestra infraestructura, solicita su colaboraci&oacute;n:</p>
    <p style="text-align: justify;">* Deshabilitando el servicio RDP, o bloquear el puerto TCP 3389, en caso de no ser utilizado. <br />* Si el servicio es necesario, restringir las conexiones TCP al puerto 3389 &uacute;nicamente desde IPs de confianza. <br />* Utilizar credenciales robustas para el acceso al servidor remoto.</p>
    <p style="text-align: justify;"><br />Adicionalmente, en caso de que este servicio/puerto se encuentre habilitado sin su conocimiento, considerar que el equipo podr&iacute;a encontrarse comprometido; de ser as&iacute;, una revisi&oacute;n m&aacute;s minuciosa debe ser realizada para determinar las causas de la incidencia. Si existe una red interna nateada a trav&eacute;s de la IP ofensora, todos los equipos de esta red deben ser revisados, con el fin de localizar al equipo que presente el servicio activo.</p>
    <p style="text-align: justify;">M&aacute;s informaci&oacute;n sobre la vulnerabilidad open RDP (RDP accesible) puede ser encontrada los siguientes enlaces:</p>
    ')
    || TO_CLOB('
    <p>* [https://rdpscan.shadowserver.org/](https://rdpscan.shadowserver.org/)<br />* [https://blogs.technet.microsoft.com/srd/2012/03/13/cve-2012-0002-a-closer-look-at-ms12-020s-critical-issue/](https://blogs.technet.microsoft.com/srd/2012/03/13/cve-2012-0002-a-closer-look-at-ms12-020s-critical-issue/)</p>
    <p>El plazo m&aacute;ximo que otorga el Departamento de Seguridad L&oacute;gica ante esta incidencia es <strong>5 d&iacute;as laborables</strong>, posterior a &eacute;ste, y si no se ha recibido respuesta a la presente notificaci&oacute;n de su parte, se proceder&aacute; al bloqueo de recepci&oacute;n de tr&aacute;fico por la IP y puerto ofensor (TCP 3389).</p>
    <p style="text-align: justify;">Quedamos a la espera de su pronta respuesta, y agradecemos la atenci&oacute;n prestada a la presente.</p>
    <p>Para una oportuna gesti&oacute;n del incidente, favor dirigir sus respuestas al correo <strong>&lt;soporte_seguridad@telconet.ec&gt;</strong></p>
    <p>Atentamente,</p>
    <p>Soporte T&eacute;cnico <br />Telconet S.A</p>
  </body>
</html>'),
'Activo',SYSDATE,'nnaulal',null,null,null);

SET DEFINE OFF 
INSERT INTO DB_COMUNICACION.ADMI_PLANTILLA (ID_PLANTILLA, NOMBRE_PLANTILLA, CODIGO, MODULO, PLANTILLA, ESTADO, FE_CREACION, USR_CREACION, FE_ULT_MOD, USR_ULT_MOD, EMPRESA_COD)
VALUES (DB_COMUNICACION.SEQ_ADMI_PLANTILLA.NEXTVAL,'NOTIFICACIÓN_OPEN_TELNET','OPENTELN','SOPORTE',
TO_CLOB('<html>
  <head>
  </head>
  <body>
    <p>&nbsp;</p>
    <p><strong>NOTIFICACI&Oacute;N Telnet Accesible</strong><br />==============================</p>
    <p>Estimado cliente,</p>
    <p style="text-align: justify;">En reporte enviado a Telconet por ARCOTEL (Agencia de Regulaci&oacute;n y Control de las Telecomunicaciones), a trav&eacute;s de su departamento EcuCERT, le informamos que la IP <strong>{{ip}} </strong>asignada a <strong>{{login}}</strong> presenta el servicio <strong>Telnet habilitado y accesible desde Internet</strong>. El protocolo Telnet permite acceder a otro equipo para manejarlo remotamente como si estuvi&eacute;ramos sentados delante de &eacute;l. El puerto que se utiliza generalmente es el 23.</p>
    <p style="text-align: justify;">Su mayor problema es de seguridad, ya que todos los nombres de usuarios y contrase&ntilde;as necesarias para entrar en los equipos viajan por la red en texto plano (cadenas de texto sin cifrar). Esto facilita que cualquiera que esp&iacute;e el tr&aacute;fico de la red pueda obtener los nombres de usuarios y contrase&ntilde;as, y as&iacute; acceder tambi&eacute;n a todos los equipos con telnet habilitado. Por esta raz&oacute;n tener el servicio de telnet accesible desde el internet e incluso dentro de una red interna NO es seguro.</p>
    ') 
    || TO_CLOB('
    <p style="text-align: justify;">Telconet, con el fin de precautelar la disponibilidad e integridad de su servicio, el de nuestros<br />clientes y el de nuestra infraestructura, solicita su colaboraci&oacute;n:</p>
    <p style="text-align: justify;">* Deshabilitando el servicio telnet, o bloquear el puerto 23 y/o 2323, en caso de no ser utilizado. <br />* Si el servicio es necesario, restringir las conexiones TCP al puerto 23 y/o 2323 &uacute;nicamente desde IPs de confianza. <br />* Tener en cuenta que es altamente recomendado usar el servicio de SSH (Secure SHell) versi&oacute;n 2 en lugar de telnet debido a las prestaciones de seguridad que proporciona.</p>
    <p style="text-align: justify;">Adicionalmente, en caso de que este servicio/puerto se encuentre habilitado sin su conocimiento, considerar que el equipo podr&iacute;a encontrarse comprometido; de ser as&iacute;, una revisi&oacute;n m&aacute;s minuciosa debe ser realizada para determinar las causas de la incidencia. Si existe una red interna nateada a trav&eacute;s de la IP ofensora, todos los equipos de esta red deben ser revisados, con el fin de localizar al equipo que presente el servicio activo.</p>
    <p>M&aacute;s informaci&oacute;n sobre la vulnerabilidad open telnet (telnet abierto) puede ser encontrada en el siguiente enlace:</p>
    ')
    || TO_CLOB('
    <p>* [https://telnetscan.shadowserver.org/](https://telnetscan.shadowserver.org/)</p>
    <p style="text-align: justify;">El plazo m&aacute;ximo que otorga el Departamento de Seguridad L&oacute;gica ante esta incidencia es <strong>5 d&iacute;as laborables</strong>, posterior a &eacute;ste, y si no se ha recibido respuesta a la presente notificaci&oacute;n de su parte, se proceder&aacute; al bloqueo de recepci&oacute;n de tr&aacute;fico por la IP y puerto ofensor (TCP 23 - TCP 2323).</p>
    <p style="text-align: justify;">Quedamos a la espera de su pronta respuesta, y agradecemos la atenci&oacute;n prestada a la presente.</p>
    <p>Para una oportuna gesti&oacute;n del incidente, favor dirigir sus respuestas al correo <strong>&lt;soporte_seguridad@telconet.ec&gt;</strong></p>
    <p>Atentamente,</p>
    <p>Soporte T&eacute;cnico <br />Telconet S.A</p>
  </body>
</html>'),
'Activo',SYSDATE,'nnaulal',null,null,null);

SET DEFINE OFF 
INSERT INTO DB_COMUNICACION.ADMI_PLANTILLA (ID_PLANTILLA, NOMBRE_PLANTILLA, CODIGO, MODULO, PLANTILLA, ESTADO, FE_CREACION, USR_CREACION, FE_ULT_MOD, USR_ULT_MOD, EMPRESA_COD)
VALUES (DB_COMUNICACION.SEQ_ADMI_PLANTILLA.NEXTVAL,'NOTIFICACIÓN_OPEN_TFTP','OPENTFTP','SOPORTE',
TO_CLOB('<html>
  <head>
  </head>
  <body>
    <p>&nbsp;</p>
    <p><strong>NOTIFICACI&Oacute;N TFTP Accesible</strong><br />==============================</p>
    <p>Estimado cliente,</p>
    <p style="text-align: justify;">En reporte enviado a Telconet por ARCOTEL (Agencia de Regulaci&oacute;n y Control de las Telecomunicaciones), a trav&eacute;s de su departamento EcuCERT, le informamos que la IP <strong>{{ip}}</strong> asignada a <strong>{{login}}</strong> presenta el servicio <strong>TFTP habilitado y accesible desde Internet</strong>. TFTP (Trivial File Transfer Protocol) es un protocolo de transferencia muy simple; utilizado para transferir peque&ntilde;os archivos entre ordenadores en una red, no posee seguridad, ni mecanismos de control de acceso, no proporciona ning&uacute;n medio para validar la identidad de una computadora que solicita transferencias de archivos. Dado que TFTP no requiere autenticaci&oacute;n, el proceso para que una m&aacute;quina se convierta en una computadora admitida en la red puede ser un proceso relativamente simple, solo debe enviar una solicitud al servidor, y como no hay forma de que TFTP compruebe si la computadora es leg&iacute;tima o no, esta m&aacute;quina podr&iacute;a convertirse en una m&aacute;quina admitida en la red.</p>
    <p style="text-align: justify;">Adem&aacute;s de la posibilidad de que informaci&oacute;n sensible est&eacute; disponible, los dispositivos con este servicio expuesto tienen el potencial de ser usados en ataques de UDP amplification con un factor de amplificaci&oacute;n mayor que otros protocolos de Internet y puede permitir a los atacantes utilizar estos servidores abiertos al p&uacute;blico para amplificar su tr&aacute;fico, de manera similar a otros ataques DDoS (Distributed Denial of Service).</p>
    ') 
    || TO_CLOB('
    <p style="text-align: justify;">Para probar que su dispositivo tiene TFTP habilitado, puede probar el comando: <br />&gt; `tftp {ip} 69`</p>
    <p style="text-align: justify;">En la l&iacute;nea de comando "tftp&gt;", tipear get [nombre-archivo-aleatorio]. Si el TFTP est&aacute; corriendo, usted probablemente ver&aacute; un c&oacute;digo de error en la respuesta.<br />Notar que tal vez necesite hacer un tcpdump para ver la respuesta del comando en caso de que &eacute;sta sea enviada a otro puerto diferente al 69 UDP.</p>
    <p style="text-align: justify;">Telconet, con el fin de precautelar la disponibilidad e integridad de su servicio, el de nuestros<br />clientes y el de nuestra infraestructura, solicita su colaboraci&oacute;n:</p>
    <p>* Deshabilitando el servicio TFTP, o bloquear el puerto 69, en caso de no ser utilizado. <br />* Si el servicio es necesario, restringir las conexiones UDP al puerto 69 &uacute;nicamente desde IPs de confianza.</p>
    <p style="text-align: justify;">En caso de que este servicio/puerto se encuentre habilitado sin su conocimiento, considerar que el equipo podr&iacute;a encontrarse comprometido; de ser as&iacute;, una revisi&oacute;n m&aacute;s minuciosa debe ser realizada para determinar las causas de la incidencia. Si existe una red interna nateada a trav&eacute;s de la IP ofensora, todos los equipos de esta red deben ser revisados, con el fin de localizar al equipo que presente el servicio activo.</p>
    <p style="text-align: justify;">M&aacute;s informaci&oacute;n sobre la vulnerabilidad en el servicio TFTP puede ser encontrado en los siguientes enlaces:</p>
    ')
    || TO_CLOB('
    <p>* [https://tftpscan.shadowserver.org/](https://tftpscan.shadowserver.org/)<br />* [https://blogs.akamai.com/2016/06/new-ddos-reflectionamplification-method-exploits-tftp.html](https://blogs.akamai.com/2016/06/new-ddos-reflectionamplification-method-exploits-tftp.html)<br />* [https://www.us-cert.gov/ncas/alerts/TA14-017A](https://www.us-cert.gov/ncas/alerts/TA14-017A)</p>
    <p style="text-align: justify;">El plazo m&aacute;ximo que otorga el Departamento de Seguridad L&oacute;gica ante esta incidencia es <strong>5 d&iacute;as laborables</strong>, posterior a &eacute;ste, y si no se ha recibido respuesta a la presente notificaci&oacute;n de su parte, se proceder&aacute; al bloqueo de recepci&oacute;n de tr&aacute;fico por la IP y puerto ofensor (UDP 69).</p>
    <p style="text-align: justify;">Quedamos a la espera de su pronta respuesta, y agradecemos la atenci&oacute;n prestada a la presente.</p>
    <p>Para una oportuna gesti&oacute;n del incidente, favor dirigir sus respuestas al correo <strong>&lt;soporte_seguridad@telconet.ec&gt;</strong></p>
    <p>Atentamente,</p>
    <p>Soporte T&eacute;cnico <br />Telconet S.A</p>
  </body>
</html>'),
'Activo',SYSDATE,'nnaulal',null,null,null);

SET DEFINE OFF 
INSERT INTO DB_COMUNICACION.ADMI_PLANTILLA (ID_PLANTILLA, NOMBRE_PLANTILLA, CODIGO, MODULO, PLANTILLA, ESTADO, FE_CREACION, USR_CREACION, FE_ULT_MOD, USR_ULT_MOD, EMPRESA_COD)
VALUES (DB_COMUNICACION.SEQ_ADMI_PLANTILLA.NEXTVAL,'NOTIFICACIÓN_SITIO_WEB_USADO_PARA_PHISHING','PHISHING','SOPORTE',
TO_CLOB('<html>
  <head>
  </head>
  <body>
    <p><strong>NOTIFICACI&Oacute;N Sitio web usado para Phishing</strong><br />===================================</p>
    <p>Estimado cliente,</p>
    <p style="text-align: justify;">En ticket num <strong>{{ticket}}</strong> enviado a Telconet por ARCOTEL (Agencia de Regulaci&oacute;n y Control de las Telecomunicaciones), a trav&eacute;s de su departamento EcuCERT, le informamos que el sitio web <strong>{{sitio_web}}</strong> con IP <strong>{{ip}}</strong> asignada a <strong>{{login}}</strong> se encuentra comprometido. En las verificaciones realizadas, se observa que el sitio est&aacute; siendo utilizado para realizar actividades de Phishing. A continuaci&oacute;n, evidencia de la visualizaci&oacute;n actual del sitio:</p>
    <p><strong>{{evidencia}}</strong></p>
        ') 
    || TO_CLOB('
    <p style="text-align: justify;">Telconet, con el fin de precautelar la disponibilidad e integridad de su servicio, el de nuestros clientes y el de nuestra infraestructura, solicita su colaboraci&oacute;n para dar de baja el sitio web fraudulento, dar por terminada su disponibilidad en Internet y finalizar la transmisi&oacute;n de todos los emails vinculados a dicho sitio.</p>
    <p style="text-align: justify;">Les solicitamos que por favor se verifique si esta IP est&aacute; siendo usada para alg&uacute;n servicio web autorizado y validar la aplicaci&oacute;n de controles necesarios.</p>
    <p style="text-align: justify;">Adicional a estas revisiones, si la aplicaci&oacute;n se encuentra desarrollada sobre alg&uacute;n gestor de contenido (CMS) como Joomla, Drupal, Wordpress, etc, asegurarse que se encuentran instaladas las actualizaciones de seguridad m&aacute;s recientes, tanto del CMS como de los plugins que se est&eacute;n utilizando.</p>
    <p style="text-align: justify;">M&aacute;s informaci&oacute;n sobre la vulnerabilidad y c&oacute;mo realizar la revisi&oacute;n de las aplicaciones comprometidas puede ser encontrada en los siguientes enlaces:</p>
        ')
    || TO_CLOB('
    <p>* [https://docs.typo3.org/typo3cms/SecurityGuide/HackedSite/Detect/Index.html](https://docs.typo3.org/typo3cms/SecurityGuide/HackedSite/Detect/Index.html)<br />* [https://www.wordfence.com/blog/2016/04/hackers-compromised-wordpress sites/](https://www.wordfence.com/blog/2016/04/hackers-compromised-wordpress-sites/)</p>
    <p style="text-align: justify;">El plazo m&aacute;ximo que otorga el Departamento de Seguridad L&oacute;gica ante esta incidencia es <strong>1 d&iacute;a laboral</strong>, posterior a &eacute;ste, y si no se ha recibido respuesta a la presente notificaci&oacute;n de su parte, se proceder&aacute; a comunicar al ente regulador.</p>
    <p style="text-align: justify;">Quedamos a la espera de su pronta respuesta, y agradecemos la atenci&oacute;n prestada a la presente.</p>
    <p>Para una oportuna gesti&oacute;n del incidente, favor dirigir sus respuestas al correo <strong>&lt;soporte_seguridad@telconet.ec&gt;</strong></p>
    <p>Atentamente,</p>
    <p>Soporte T&eacute;cnico</p>
    <p>Telconet S.A</p>
  </body>
</html>'),
'Activo',SYSDATE,'nnaulal',null,null,null);

SET DEFINE OFF 
INSERT INTO DB_COMUNICACION.ADMI_PLANTILLA (ID_PLANTILLA, NOMBRE_PLANTILLA, CODIGO, MODULO, PLANTILLA, ESTADO, FE_CREACION, USR_CREACION, FE_ULT_MOD, USR_ULT_MOD, EMPRESA_COD)
VALUES (DB_COMUNICACION.SEQ_ADMI_PLANTILLA.NEXTVAL,'NOTIFICACIÓN_OPEN_PORTMAPPER','PORTMAPPER','SOPORTE',
TO_CLOB('<html>
  <head>
  </head>
  <body>
    <p><strong>NOTIFICACI&Oacute;N Open Portmapper</strong><br />============================</p>
    <p>Estimado cliente,</p>
    <p style="text-align: justify;">En reporte enviado a Telconet por ARCOTEL (Agencia de Regulaci&oacute;n y Control de las Telecomunicaciones), a trav&eacute;s de su departamento EcuCERT, le informamos que la IP <strong>{{ip}}</strong> asignada a <strong>{{login}}</strong> est&aacute; prestando el servicio de <strong>mapeo de puertos, tambi&eacute;n conocido como rpcbind, accesible desde el internet a trav&eacute;s del puerto UDP 111</strong>. Este servicio permite al usuario obtener la versi&oacute;n y puerto de otros servicios RPC que tambi&eacute;n se est&eacute;n ejecutando sobre el servidor. As&iacute; mismo, este servicio es usualmente utilizado para realizar ataques de amplificaci&oacute;n de denegaci&oacute;n de servicio (DDoS) cuando se encuentra indebidamente configurado.</p>
    ') 
    || TO_CLOB('
    <p style="text-align: justify;">Telconet, con el fin de precautelar la disponibilidad e integridad de su servicio, el de nuestros clientes y el de nuestra infraestructura, solicita su colaboraci&oacute;n restringiendo el acceso al servicio s&oacute;lo a IPs de confianza y que necesiten acceder al mismo y en caso de que el servicio no est&eacute; siendo utilizado, desactivarlo por completo. En caso de que este servicio/puerto se encuentre habilitado sin su conocimiento, considerar que el equipo podr&iacute;a encontrarse comprometido; de ser as&iacute;, una revisi&oacute;n m&aacute;s minuciosa debe ser realizada para determinar las causas de la incidencia. Si existe una red interna nateada a trav&eacute;s de la IP ofensora, todos los equipos de esta red deben ser revisados, con el fin de localizar al equipo que presente el servicio habilitado.</p>
    <p style="text-align: justify;">M&aacute;s informaci&oacute;n sobre la vulnerabilidad Open Portmapper y c&oacute;mo configurar de manera segura los&nbsp;dispositivos afectados puede ser encontrada en los siguientes enlaces:</p>
    ')
    || TO_CLOB('
    <p>* [https://en.wikipedia.org/wiki/Portmap](https://en.wikipedia.org/wiki/Portmap)<br />* [http://blog.level3.com/security/a-new-ddos-reflection-attack-portmapper-an-early-warning-to-the-<br />industry/](http://blog.level3.com/security/a-new-ddos-reflection-attack-portmapper-an-early-warning-to-the-<br />industry/)<br />* [https://www.sans.org/security-resources/idfaq/blocking.php](https://www.sans.org/security-resources/idfaq/blocking.php)</p>
    <p style="text-align: justify;">El plazo m&aacute;ximo que otorga el Departamento de Seguridad L&oacute;gica ante esta incidencia es <strong>5 d&iacute;as laborables</strong>, posterior a &eacute;ste, y si no se ha recibido respuesta a la presente notificaci&oacute;n de su parte, se proceder&aacute; al bloqueo de recepci&oacute;n de tr&aacute;fico por la IP y puerto ofensor (111 TCP y UDP).</p>
    <p style="text-align: justify;">Quedamos a la espera de su pronta respuesta, y agradecemos la atenci&oacute;n prestada a la presente.</p>
    <p>Para una oportuna gesti&oacute;n del incidente, favor dirigir sus respuestas al correo <strong>&lt;soporte_seguridad@telconet.ec&gt;</strong></p>
    <p>Atentamente,</p>
    <p>Soporte T&eacute;cnico</p>
    <p>Telconet S.A</p>
    </body>
</html>'),
'Activo',SYSDATE,'nnaulal',null,null,null);

SET DEFINE OFF 
INSERT INTO DB_COMUNICACION.ADMI_PLANTILLA (ID_PLANTILLA, NOMBRE_PLANTILLA, CODIGO, MODULO, PLANTILLA, ESTADO, FE_CREACION, USR_CREACION, FE_ULT_MOD, USR_ULT_MOD, EMPRESA_COD)
VALUES (DB_COMUNICACION.SEQ_ADMI_PLANTILLA.NEXTVAL,'NOTIFICACIÓN_OPEN_QOTD','QOTD','SOPORTE',
TO_CLOB('<html>
  <head>
  </head>
  <body>
    <p><strong>NOTIFICACI&Oacute;N Open QOTD</strong><br />======================</p>
    <p>Estimado cliente,</p>
    <p style="text-align: justify;">En reporte enviado a Telconet por ARCOTEL (Agencia de Regulaci&oacute;n y Control de las Telecomunicaciones), a trav&eacute;s de su departamento EcuCERT, le informamos que la IP <strong>{{ip}}</strong> asignada a <strong>{{login}}</strong> mantiene el servicio **QOTD (Quote Of The Day) habilitado y accesible desde Internet en el puerto TCP y UDP 17**. QOTD es un protocolo utilizado para fines de prueba y depuraci&oacute;n que utiliza el puerto 17 en TCP y UDP; al abrir una conexi&oacute;n TCP, el servidor responde con una frase aleatoria y cierra la conexi&oacute;n. Si la conexi&oacute;n es UDP, el servidor env&iacute;a una frase aleatoria cada vez que recibe un datagrama desde el host hasta que se cierre la conexi&oacute;n. Este comportamiento tiene el potencial de ser utilizado en ataques de amplificaci&oacute;n de Denegaci&oacute;n de Servicio (DoS) cuando el servicio se encuentra accesible desde el internet.</p>
    ') 
    || TO_CLOB('
    <p style="text-align: justify;">Telconet, con el fin de precautelar la disponibilidad e integridad de su servicio, el de nuestros clientes y el de nuestra infraestructura, solicita su colaboraci&oacute;n deshabilitando el servicio QOTD, o bloquear completamente el puerto ofensor en caso de no ser utilizado. Si el servicio es necesario, restringir las conexiones TCP y UDP al puerto 17 &uacute;nicamente desde IPs de confianza.</p>
    <p style="text-align: justify;">En caso de que este servicio/puerto se encuentre habilitado sin su conocimiento, considerar que el equipo podr&iacute;a encontrarse comprometido; de ser as&iacute;, una revisi&oacute;n m&aacute;s minuciosa debe ser realizada para determinar las causas de la incidencia. Si existe una red interna nateada a trav&eacute;s de la IP ofensora, todos los equipos de esta red deben ser revisados, con el fin de localizar al equipo que presente el servicio activo.</p>
    <p style="text-align: justify;">M&aacute;s informaci&oacute;n sobre la vulnerabilidad en el servicio QOTD y c&oacute;mo configurar de manera segura los dispositivos afectados puede ser encontrada en los siguientes enlaces:</p>
    ')
    || TO_CLOB('
    <p>* [http://www.ecucert.gob.ec/qotd.html#ANCHOR_Box1](http://www.ecucert.gob.ec/qotd.html#ANCHOR_Box1)<br />* [https://tools.ietf.org/html/rfc865](https://tools.ietf.org/html/rfc865)<br />* [http://www.windowsnetworking.com/articles-tutorials/windows-7/Windows-7-Simple-TCPIP-<br />Services-What-How.html](http://www.windowsnetworking.com/articles-tutorials/windows-7/Windows-7-Simple-TCPIP-<br />Services-What-How.html)</p>
    <p style="text-align: justify;">El plazo m&aacute;ximo que otorga el Departamento de Seguridad L&oacute;gica ante esta incidencia es <strong>5 d&iacute;as laborables</strong>, posterior a &eacute;ste, y si no se ha recibido respuesta a la presente notificaci&oacute;n de su parte, se proceder&aacute; al bloqueo de recepci&oacute;n de tr&aacute;fico por la IP y puerto ofensor (17 TCP y UDP).</p>
    <p style="text-align: justify;">Quedamos a la espera de su pronta respuesta, y agradecemos la atenci&oacute;n prestada a la presente.</p>
    <p>Para una oportuna gesti&oacute;n del incidente, favor dirigir sus respuestas al correo <strong>&lt;soporte_seguridad@telconet.ec&gt;</strong></p>
    <p>Atentamente,</p>
    <p>Soporte T&eacute;cnico</p>
    <p>Telconet S.A</p>
    </body>
</html>'),
'Activo',SYSDATE,'nnaulal',null,null,null);

SET DEFINE OFF 
INSERT INTO DB_COMUNICACION.ADMI_PLANTILLA (ID_PLANTILLA, NOMBRE_PLANTILLA, CODIGO, MODULO, PLANTILLA, ESTADO, FE_CREACION, USR_CREACION, FE_ULT_MOD, USR_ULT_MOD, EMPRESA_COD)
VALUES (DB_COMUNICACION.SEQ_ADMI_PLANTILLA.NEXTVAL,'NOTIFICACIÓN_OPEN_REDIS','REDIS','SOPORTE',
TO_CLOB('<html>
  <head>
  </head>
  <body>
    <p><strong>NOTIFICACI&Oacute;N OPEN Redis</strong><br />=======================</p>
    <p>Estimado cliente,</p>
    <p style="text-align: justify;">En reporte enviado a Telconet por ARCOTEL (Agencia de Regulaci&oacute;n y Control de las Telecomunicaciones), a trav&eacute;s de su departamento EcuCERT, le informamos que la IP <strong>{{ip}}</strong> asignada a <strong>{{login}}</strong> presenta el servicio de datos estructurados <strong>Redis habilitado y accesible desde Internet por el puerto TCP 6379</strong>. Al encontrarse el servicio accesible, es posible recuperar informaci&oacute;n sobre el sistema operativo y el servicio de datos estructurados.</p>
    <p style="text-align: justify;">Telconet, con el fin de precautelar la disponibilidad e integridad de su servicio, el de nuestros clientes y el de nuestra infraestructura, solicita su colaboraci&oacute;n restringiendo las conexiones al puerto TCP 6379 y configurando el acceso autenticado a las bases de datos, en caso de no encontrarse configurado. En caso de que el servicio no est&eacute; siendo utilizado, deshabilitarlo y bloquear completamente el acceso al puerto ofensor.</p>
    ') 
    || TO_CLOB('
    <p style="text-align: justify;">En caso de que este servicio/puerto se encuentre habilitado sin su conocimiento, considerar que el equipo podr&iacute;a encontrarse comprometido; de ser as&iacute;, una revisi&oacute;n m&aacute;s minuciosa debe ser realizada para determinar las causas de la incidencia. Si existe una red interna nateada a trav&eacute;s de la IP ofensora, todos los equipos de esta red deben ser revisados, con el fin de localizar al equipo que presente el servicio activo.</p>
    <p style="text-align: justify;">M&aacute;s informaci&oacute;n sobre la vulnerabilidad en el servicio Redis y c&oacute;mo configurar de manera segura los dispositivos afectados puede ser encontrada en el siguiente enlace:</p>
    <p>* [http://redis.io/topics/security](http://redis.io/topics/security)</p>
    ')
    || TO_CLOB('
    <p style="text-align: justify;">El plazo m&aacute;ximo que otorga el Departamento de Seguridad L&oacute;gica ante esta incidencia es <strong>5 d&iacute;as laborables</strong>, posterior a &eacute;ste, y si no se ha recibido respuesta a la presente notificaci&oacute;n de su parte, se proceder&aacute; al bloqueo de recepci&oacute;n de tr&aacute;fico por la IP y puerto ofensor (TCP 6379).</p>
    <p style="text-align: justify;">Quedamos a la espera de su pronta respuesta, y agradecemos la atenci&oacute;n prestada a la presente.</p>
    <p>Para una oportuna gesti&oacute;n del incidente, favor dirigir sus respuestas al correo <strong>&lt;soporte_seguridad@telconet.ec&gt;</strong></p>
    <p>Atentamente,</p>
    <p>Soporte T&eacute;cnico</p>
    <p>Telconet S.A</p>
    </body>
</html>'),
'Activo',SYSDATE,'nnaulal',null,null,null);

SET DEFINE OFF 
INSERT INTO DB_COMUNICACION.ADMI_PLANTILLA (ID_PLANTILLA, NOMBRE_PLANTILLA, CODIGO, MODULO, PLANTILLA, ESTADO, FE_CREACION, USR_CREACION, FE_ULT_MOD, USR_ULT_MOD, EMPRESA_COD)
VALUES (DB_COMUNICACION.SEQ_ADMI_PLANTILLA.NEXTVAL,'NOTIFICACIÓN_OPEN_SNMP','SNMP','SOPORTE',
TO_CLOB('<html>
  <head>
  </head>
  <body>
    <p><strong>NOTIFICACI&Oacute;N OPEN SNMP</strong><br />==============================</p>
    <p style="text-align: justify;">Estimado cliente,<br />En reporte enviado a Telconet por ARCOTEL (Agencia de Regulaci&oacute;n y Control de las Telecomunicaciones), a trav&eacute;s de su departamento EcuCERT, le informamos que la IP <strong>{{ip}}</strong> <strong>tiene el servicio SNMPv2 expuesto en internet y respondiendo a la comunidad &ldquo;public&rdquo; por el puerto UDP 161</strong>&nbsp;y podr&iacute;a ser consultado de manera libre por cualquiera que lo desee. A trav&eacute;s de SNMPv2 es posible conocer informaci&oacute;n sensible de un host o dispositivo, como su nombre, descripci&oacute;n, interfaces de red, propiedades del sistema, entre otros, puesto no cifra la informaci&oacute;n que transmite. Esta caracter&iacute;stica, usada junto con el comando getbulkrequest, tiene el potencial de ser utilizada en ataques de amplificaci&oacute;n de Denegaci&oacute;n de Servicio (DoS) cuando se encuentran accesibles desde el internet.</p>
    <p>La comprobaci&oacute;n se puede realizar en una m&aacute;quina con Linux usando el siguiente comando: <br />`snmpget -c public -v 2c {ip} 1.3.6.1.2.1.1.1.0` para obtener el OID sysDescr <br />`snmpget -c public -v 2c {ip} 1.3.6.1.2.1.1.5.0` para obtener el OID sysName</p>
    ') 
    || TO_CLOB('
    <p style="text-align: justify;">Telconet, con el fin de precautelar la disponibilidad e integridad de su servicio, el de nuestros clientes y el de nuestra infraestructura, solicita su colaboraci&oacute;n para realizar los controles necesarios cambiando el nombre de la comunidad &ldquo;public&rdquo; por uno m&aacute;s robusto. As&iacute; mismo, considerar la actualizaci&oacute;n del servicio SNMP a su versi&oacute;n m&aacute;s actual (v3). De no ser posible realizar la actualizaci&oacute;n de manera inmediata, restringir el acceso al servicio s&oacute;lo a IPs de confianza y publicar el servicio en internet s&oacute;lo de ser necesario.</p>
    <p style="text-align: justify;">En caso de que este servicio se encuentre habilitado sin su conocimiento, considerar que el dispositivo podr&iacute;a encontrarse comprometido; de ser as&iacute;, una revisi&oacute;n m&aacute;s minuciosa debe ser realizada para determinar las causas de la incidencia. Si existe una red interna nateada a trav&eacute;s de la IP ofensora, todos los equipos de esta red deben ser revisados, con el fin de localizar al equipo que presente el servicio activo.</p>
    <p style="text-align: justify;">M&aacute;s informaci&oacute;n sobre la vulnerabilidad en servicios SNMP y c&oacute;mo configurar de manera segura los dispositivos afectados puede ser encontrada en los siguientes enlaces:</p>
    ')
    || TO_CLOB('
    <p>* [http://www.ecucert.gob.ec/open---snmp.html](http://www.ecucert.gob.ec/open---snmp.html)<br />* [http://www.rediris.es/difusion/publicaciones/boletin/50-51/ponencia16.html](http://www.rediris.es/difusion/publicaciones/boletin/50-51/ponencia16.html)<br />* [https://support.microsoft.com/en-us/kb/315154](https://support.microsoft.com/en-us/kb/315154)<br />* [https://penturalabs.wordpress.com/2010/10/07/secure-your-snmp/](https://penturalabs.wordpress.com/2010/10/07/secure-your-snmp/)</p>
    <p style="text-align: justify;">El plazo m&aacute;ximo que otorga el Departamento de Seguridad L&oacute;gica ante esta incidencia es <strong>5 d&iacute;as laborables</strong>, posterior a &eacute;ste, y si no se ha recibido respuesta a la presente notificaci&oacute;n de su parte, se proceder&aacute; al bloqueo de recepci&oacute;n de tr&aacute;fico por la IP y puerto ofensor (UDP 161).</p>
    <p style="text-align: justify;">Quedamos a la espera de su pronta respuesta, y agradecemos la atenci&oacute;n prestada a la presente.</p>
    <p>Para una oportuna gesti&oacute;n del incidente, favor dirigir sus respuestas al correo <strong>&lt;soporte_seguridad@telconet.ec&gt;</strong></p>
    <p>Atentamente,</p>
    <p>Soporte T&eacute;cnico</p>
    <p>Telconet S.A</p>
    </body>
</html>'),
'Activo',SYSDATE,'nnaulal',null,null,null);

SET DEFINE OFF 
INSERT INTO DB_COMUNICACION.ADMI_PLANTILLA (ID_PLANTILLA, NOMBRE_PLANTILLA, CODIGO, MODULO, PLANTILLA, ESTADO, FE_CREACION, USR_CREACION, FE_ULT_MOD, USR_ULT_MOD, EMPRESA_COD)
VALUES (DB_COMUNICACION.SEQ_ADMI_PLANTILLA.NEXTVAL,'NOTIFICACIÓN_DE_IP_GENERADORA_DE_SPAM_UCEPROTECT','SPAMUCEP','SOPORTE',
TO_CLOB('<html>
  <head>
  </head>
  <body>
    <p><strong>NOTIFICACI&Oacute;N DE IP GENERADORA DE SPAM (RBL UCEPROTECT)</strong><br />======================================================</p>
    <p>Estimado Cliente,</p>
    <p style="text-align: justify;">Le informamos que la IP <strong>{{ip}}</strong> asignada a <strong>{{login}}</strong> se encuentra <strong>listada en la RBL UCEPROTECT</strong>&nbsp;por estar generando SPAM (correo electr&oacute;nico no deseado), raz&oacute;n por la cual solicitamos su justificaci&oacute;n del uso del puerto TCP 25 (SMTP). Una mala configuraci&oacute;n de este puerto, as&iacute; como la infecci&oacute;n con alg&uacute;n tipo de virus o malware de uno o m&aacute;s equipos de su red interna, que hagan uso de la IP reportada para su salida a internet, puede dar paso al env&iacute;o masivo de correos electr&oacute;nicos no deseados a trav&eacute;s del equipo o la red afectada.</p>
    <p>El enlistamiento de la IP puede ser verificado en el siguiente enlace: [http://www.uceprotect.net/en/rblcheck.php](http://www.uceprotect.net/en/rblcheck.php)</p>
    ') 
    || TO_CLOB('
    <p style="text-align: justify;">Telconet, con el fin de precautelar la disponibilidad e integridad de su servicio, el de nuestros clientes y el de nuestra infraestructura, solicita su colaboraci&oacute;n revisando los equipos de red interna en busca de malware que pudiera estar comprometiendo al equipo, y desinfectarlo completamente. En caso de desconocer si el equipo est&aacute; infectado, recomendamos reinstalarlo completamente; adem&aacute;s de mantener siempre el equipo con un antivirus actualizado. Si existe una red interna que sale a trav&eacute;s de la IP ofensora, todos los equipos de esta red deben ser revisados, puesto, muy probablemente, varios de los equipos de esta red se encuentren infectados.</p>
    <p style="text-align: justify;">Si la IP reportada pertenece a un servidor de correos, se deben revisar las medidas antiSPAM implementadas en el mismo, a fin de evitar el env&iacute;o de este tipo de correos.</p>
    ')
    || TO_CLOB('
    <p style="text-align: justify;">El plazo m&aacute;ximo que otorga el Departamento de Seguridad L&oacute;gica ante esta incidencia es <strong>2 d&iacute;as laborables</strong>, posterior a &eacute;ste, y si no se ha recibido respuesta a la presente notificaci&oacute;n de su parte, se proceder&aacute; al bloqueo de env&iacute;o de tr&aacute;fico por la IP y puerto ofensor (TCP 25).</p>
    <p style="text-align: justify;">Quedamos a la espera de su pronta respuesta, y agradecemos la atenci&oacute;n prestada a la presente.</p>
    <p>Para una oportuna gesti&oacute;n del incidente, favor dirigir sus respuestas al correo <strong>&lt;soporte_seguridad@telconet.ec&gt;</strong></p>
    <p>Atentamente,</p>
    <p>Soporte T&eacute;cnico</p>
    <p>Telconet S.A</p>
    </body>
</html>'),
'Activo',SYSDATE,'nnaulal',null,null,null);

SET DEFINE OFF 
INSERT INTO DB_COMUNICACION.ADMI_PLANTILLA (ID_PLANTILLA, NOMBRE_PLANTILLA, CODIGO, MODULO, PLANTILLA, ESTADO, FE_CREACION, USR_CREACION, FE_ULT_MOD, USR_ULT_MOD, EMPRESA_COD)
VALUES (DB_COMUNICACION.SEQ_ADMI_PLANTILLA.NEXTVAL,'NOTIFICACIÓN_OPEN_SSDP','SSDP','SOPORTE',
TO_CLOB('<html>
  <head>
  </head>
  <body>
    <p><strong>NOTIFICACI&Oacute;N OPEN SSDP</strong><br />======================</p>
    <p>Estimado cliente,</p>
    <p style="text-align: justify;">En reporte enviado a Telconet por ARCOTEL (Agencia de Regulaci&oacute;n y Control de las Telecomunicaciones), a trav&eacute;s de su departamento EcuCERT, le informamos que la IP <strong>{{ip}}</strong> asignada a <strong>{{login}}</strong> presenta el servicio <strong>SSDP habilitado y accesible desde Internet por el puerto UDP 1900</strong>. SSDP es un protocolo que sirve para la b&uacute;squeda de dispositivos UPnP (Universal Plug and Play) en una red y utiliza el puerto UDP 1900 para anunciar los servicios de un dispositivo. Este comportamiento tiene el potencial ser utilizado en ataques de amplificaci&oacute;n de Denegaci&oacute;n de Servicio (DoS) cuando se encuentran accesibles desde el internet.</p>
    <p style="text-align: justify;">Telconet, con el fin de precautelar la disponibilidad e integridad de su servicio, el de nuestros clientes y el de nuestra infraestructura, solicita su colaboraci&oacute;n deshabilitando el servicio UPnP, o bloquear completamente el puerto ofensor, en caso de no ser utilizado. Si el servicio es necesario, restringir las conexiones UDP al puerto 1900 &uacute;nicamente desde IPs de confianza.</p>
    ') 
    || TO_CLOB('
    <p style="text-align: justify;">En caso de que este servicio/puerto se encuentre habilitado sin su conocimiento, considerar que el equipo podr&iacute;a encontrarse comprometido; de ser as&iacute;, una revisi&oacute;n m&aacute;s minuciosa debe ser realizada para determinar las causas de la incidencia. Si existe una red interna nateada a trav&eacute;s de la IP ofensora, todos los equipos de esta red deben ser revisados, con el fin de localizar al equipo que presente el servicio activo.</p>
    <p style="text-align: justify;">M&aacute;s informaci&oacute;n sobre la vulnerabilidad en el servicio SSDP y c&oacute;mo configurar de manera segura los dispositivos afectados puede ser encontrada en los siguientes enlaces:</p>
    <p>* [http://www.ecucert.gob.ec/open_ssdp.html#ANCHOR_Box1](http://www.ecucert.gob.ec/open_ssdp.html#ANCHOR_Box1)</p> 
    ')
    || TO_CLOB('
    <p style="text-align: justify;">El plazo m&aacute;ximo que otorga el Departamento de Seguridad L&oacute;gica ante esta incidencia es <strong>5 d&iacute;as laborables</strong>, posterior a &eacute;ste, y si no se ha recibido respuesta a la presente notificaci&oacute;n de su parte, se proceder&aacute; al bloqueo de recepci&oacute;n de tr&aacute;fico por la IP y puerto ofensor (UDP 1900).</p>
    <p style="text-align: justify;">Quedamos a la espera de su pronta respuesta, y agradecemos la atenci&oacute;n prestada a la presente.</p>
    <p>Para una oportuna gesti&oacute;n del incidente, favor dirigir sus respuestas al correo <strong>&lt;soporte_seguridad@telconet.ec&gt;</strong></p>
    <p>Atentamente,</p>
    <p>Soporte T&eacute;cnico</p>
    <p>Telconet S.A</p>
    </body>
</html>'),
'Activo',SYSDATE,'nnaulal',null,null,null);

SET DEFINE OFF 
INSERT INTO DB_COMUNICACION.ADMI_PLANTILLA (ID_PLANTILLA, NOMBRE_PLANTILLA, CODIGO, MODULO, PLANTILLA, ESTADO, FE_CREACION, USR_CREACION, FE_ULT_MOD, USR_ULT_MOD, EMPRESA_COD)
VALUES (DB_COMUNICACION.SEQ_ADMI_PLANTILLA.NEXTVAL,'NOTIFICACIÓN_SSL-TLS_FREAK','SSLFREAK','SOPORTE',
TO_CLOB('<html>
  <head>
  </head>
  <body>
    <p><strong>NOTIFICACI&Oacute;N SSL/TLS FREAK</strong><br />==========================</p>
    <p>Estimado cliente,</p>
    <p style="text-align: justify;">En reporte enviado a Telconet por ARCOTEL (Agencia de Regulaci&oacute;n y Control de las Telecomunicaciones), a trav&eacute;s de su departamento EcuCERT, le informamos que la IP <strong>{{ip}}</strong> asignada a <strong>{{login}}</strong> <strong>acepta suites de cifrado RSA_EXPORT en el servicio HTTPS (puerto 443)</strong>, lo que permite a un atacante que utilice t&eacute;cnicas de hombre en el medio (MiTM) forzar el uso de claves RSA de 512 bits (consideradas d&eacute;biles) y proceder a descifrar el contenido de la sesi&oacute;n SSL; adicional, este protocolo ha sido marcado como obsoleto por la IETF, por lo que su uso no es recomendado.</p>
    <p>Si el servidor soporta algoritmos de cifrado tipo `RSA\_EXPORT` (`TLS\_RSA\_EXPORT\_WITH\_DES40\_CBC\_SHA, TLS\_RSA\_EXPORT\_WITH\_RC4\_40\_MD5`, etc) se considera vulnerable al ataque.</p>
    ') 
    || TO_CLOB('
    <p style="text-align: justify;">Telconet, con el fin de precautelar la disponibilidad e integridad de su servicio, el de nuestros clientes y el de nuestra infraestructura, solicita su colaboraci&oacute;n para realizar los controles necesarios deshabilitando el soporte para los algoritmos de cifrado tipo EXPORT en la configuraci&oacute;n del dispositivo afectado.</p>
    <p style="text-align: justify;">Si existe una red interna nateada a trav&eacute;s de la IP ofensora, todos los equipos de esta red deben ser revisados, con el fin de localizar al equipo que presente la vulnerabilidad.</p>
    <p style="text-align: justify;">M&aacute;s informaci&oacute;n sobre la vulnerabilidad FREAK y c&oacute;mo configurar de manera segura los dispositivos afectados puede ser encontrada en los siguientes enlaces:</p>
    ')
    || TO_CLOB('
    <p>* [http://www.ecucert.gob.ec/freak.html#ANCHOR_Box1](http://www.ecucert.gob.ec/freak.html#ANCHOR_Box1)<br />* [https://mozilla.github.io/server-side-tls/ssl-config-generator/](https://mozilla.github.io/server-side-tls/ssl-config-generator/)<br />* [https://tools.ietf.org/html/rfc7568](https://tools.ietf.org/html/rfc7568)</p>
    <p style="text-align: justify;">El plazo m&aacute;ximo que otorga el Departamento de Seguridad L&oacute;gica ante esta incidencia es <strong>5 d&iacute;as laborables</strong>, posterior a &eacute;ste, y si no se ha recibido respuesta a la presente notificaci&oacute;n de su parte, se proceder&aacute; a comunicar al ente regulador.</p>
    <p style="text-align: justify;">Quedamos a la espera de su pronta respuesta, y agradecemos la atenci&oacute;n prestada a la presente.</p>
    <p>Para una oportuna gesti&oacute;n del incidente, favor dirigir sus respuestas al correo <strong>&lt;soporte_seguridad@telconet.ec&gt;</strong></p>
    <p>Atentamente,</p>
    <p>Soporte T&eacute;cnico</p>
    <p>Telconet S.A</p>
    </body>
</html>'),
'Activo',SYSDATE,'nnaulal',null,null,null);

SET DEFINE OFF 
INSERT INTO DB_COMUNICACION.ADMI_PLANTILLA (ID_PLANTILLA, NOMBRE_PLANTILLA, CODIGO, MODULO, PLANTILLA, ESTADO, FE_CREACION, USR_CREACION, FE_ULT_MOD, USR_ULT_MOD, EMPRESA_COD)
VALUES (DB_COMUNICACION.SEQ_ADMI_PLANTILLA.NEXTVAL,'NOTIFICACIÓN_SSLV3_POODLE','SSLV3','SOPORTE',
TO_CLOB('<html>
  <head>
  </head>
  <body>
    <p><strong>NOTIFICACI&Oacute;N SSLv3 POODLE</strong><br />=========================</p> 
    <p>Estimado cliente,</p>
    <p style="text-align: justify;">En reporte enviado a Telconet por ARCOTEL (Agencia de Regulaci&oacute;n y Control de las Telecomunicaciones), a trav&eacute;s de su departamento EcuCERT, le informamos que la IP <strong>{{ip}}</strong> asignada a <strong>{{login}}</strong> permite el <strong>uso de SSLv3 en el servicio HTTPS (puerto 443), vulnerable al ataque POODLE</strong>, el cual se aprovecha de la funci&oacute;n de negociaci&oacute;n para el uso de diferente versiones integradas en la suite de protocolos de cifrado SSL/TLS, para forzar el uso de SSL v3.0 con CBC (Cipher-block Chaining), y proceder a descifrar el contenido de una sesi&oacute;n SSL; adicional, este protocolo ha sido marcado como obsoleto por la IETF, por lo que su uso no es recomendado.</p>
    <p style="text-align: justify;">La comprobaci&oacute;n se puede realizar en una m&aacute;quina con openssl usando el siguiente comando: <br />&gt; `openssl s_client -connect {ip}:443 -ssl3`</p>
    ') 
    || TO_CLOB('
    <p style="text-align: justify;">Telconet, con el fin de precautelar la disponibilidad e integridad de su servicio, el de nuestros clientes y el de nuestra infraestructura, solicita su colaboraci&oacute;n para realizar los controles necesarios deshabilitando el soporte para SSLv3 y versiones anteriores, de manera que en la negociaci&oacute;n de protocolos s&oacute;lo est&eacute;n disponibles los m&aacute;s robustos (TLSv1.1 y TLSv1.2).</p>
    <p style="text-align: justify;">Si existe una red interna nateada a trav&eacute;s de la IP ofensora, todos los equipos de esta red deben ser revisados, con el fin de localizar al equipo que presente la vulnerabilidad.</p>
    <p style="text-align: justify;">M&aacute;s informaci&oacute;n sobre la vulnerabilidad POODLE y c&oacute;mo configurar de manera segura los dispositivos afectados puede ser encontrada en los siguientes enlaces:</p>
    <p>* [http://www.ecucert.gob.ec/poodle-de-sslv3.html](http://www.ecucert.gob.ec/poodle-de-sslv3.html)<br />* [http://disablessl3.com/](http://disablessl3.com/)<br />* [https://zmap.io/sslv3/servers.html](https://zmap.io/sslv3/servers.html)<br />* [https://tools.ietf.org/html/rfc7568](https://tools.ietf.org/html/rfc7568)</p>
    ')
    || TO_CLOB('
    <p style="text-align: justify;">El plazo m&aacute;ximo que otorga el Departamento de Seguridad L&oacute;gica ante esta incidencia es <strong>5 d&iacute;as laborables</strong>, posterior a &eacute;ste, y si no se ha recibido respuesta a la presente notificaci&oacute;n de su parte, se proceder&aacute; a comunicar al ente regulador.</p>
    <p style="text-align: justify;">Quedamos a la espera de su pronta respuesta, y agradecemos la atenci&oacute;n prestada a la presente.</p>
    <p>Para una oportuna gesti&oacute;n del incidente, favor dirigir sus respuestas al correo <strong>&lt;soporte_seguridad@telconet.ec&gt;</strong></p>
    <p>Atentamente,</p>
    <p>Soporte T&eacute;cnico</p>
    <p>Telconet S.A</p>
    </body>
</html>'),
'Activo',SYSDATE,'nnaulal',null,null,null);

SET DEFINE OFF 
INSERT INTO DB_COMUNICACION.ADMI_PLANTILLA (ID_PLANTILLA, NOMBRE_PLANTILLA, CODIGO, MODULO, PLANTILLA, ESTADO, FE_CREACION, USR_CREACION, FE_ULT_MOD, USR_ULT_MOD, EMPRESA_COD)
VALUES (DB_COMUNICACION.SEQ_ADMI_PLANTILLA.NEXTVAL,'NOTIFICACIÓN_SITIO_WEB_COMPROMETIDO','WEBSITE','SOPORTE',
TO_CLOB('<html>
  <head>
  </head>
  <body>
    <p><strong>NOTIFICACI&Oacute;N Sitio web comprometido</strong><br />===================================</p>
    <p>Estimado cliente,</p>
    <p style="text-align: justify;">En reporte enviado a Telconet por ARCOTEL (Agencia de Regulaci&oacute;n y Control de las Telecomunicaciones), a trav&eacute;s de su departamento EcuCERT, le informamos que el <strong>sitio web</strong> <strong>{{sitio_web}}</strong> con IP <strong>{{ip}}</strong> <strong>asignada a {{login}} se encuentra comprometido.</strong> Si un sitio web se encuentra comprometido puede ser utilizado para el env&iacute;o de SPAM, participaci&oacute;n en ataques DDoS, infecci&oacute;n de usuarios que acceden al sitio web, entre otros.</p>
    <p>Probablemente, el contenido comprometido se encuentre en la URL<br />&gt; {application}://{http_host}/{url}</p>
    <p style="text-align: justify;">Telconet, con el fin de precautelar la disponibilidad e integridad de su servicio, el de nuestros clientes y el de nuestra infraestructura, solicita su colaboraci&oacute;n para realizar los controles necesarios en su servidor, revisi&oacute;n de la aplicaci&oacute;n/c&oacute;digo fuente y desinfecci&oacute;n del equipo.</p>
    ') 
    || TO_CLOB('
    <p style="text-align: justify;">Adicional a estas revisiones, si la aplicaci&oacute;n se encuentra desarrollada sobre alg&uacute;n gestor de contenido (CMS) como Joomla, Drupal, Wordpress, etc, asegurarse que se encuentran instaladas las actualizaciones de seguridad m&aacute;s recientes, tanto del CMS como de los plugins que se est&eacute;n utilizando.</p>
    <p style="text-align: justify;">M&aacute;s informaci&oacute;n sobre la vulnerabilidad y c&oacute;mo realizar la revisi&oacute;n de las aplicaciones comprometidas puede ser encontrada en los siguientes enlaces:</p>
    <p>* [http://www.ecucert.gob.ec/compromised.html#ANCHOR_Box1](http://www.ecucert.gob.ec/compromised.html#ANCHOR_Box1)<br />* [http://www.whoishostingthis.com/resources/website-hacked-checklist/](http://www.whoishostingthis.com/resources/website-hacked-checklist/)<br />* [https://docs.typo3.org/typo3cms/SecurityGuide/HackedSite/Detect/Index.html](https://docs.typo3.org/typo3cms/SecurityGuide/HackedSite/Detect/Index.html)</p>
    ')
    || TO_CLOB('
    <p style="text-align: justify;">El plazo m&aacute;ximo que otorga el Departamento de Seguridad L&oacute;gica ante esta incidencia es <strong>5 d&iacute;as laborables</strong>, posterior a &eacute;ste, y si no se ha recibido respuesta a la presente notificaci&oacute;n de su parte, se proceder&aacute; a comunicar al ente regulador.</p>
    <p style="text-align: justify;">Quedamos a la espera de su pronta respuesta, y agradecemos la atenci&oacute;n prestada a la presente.</p>
    <p>Para una oportuna gesti&oacute;n del incidente, favor dirigir sus respuestas al correo <strong>&lt;soporte_seguridad@telconet.ec&gt;</strong></p>
    <p>Atentamente,</p>
    <p>Soporte T&eacute;cnico</p>
    <p>Telconet S.A</p>
    </body>
</html>'),
'Activo',SYSDATE,'nnaulal',null,null,null);
COMMIT;

/