--Creación de las plantillas que usan ECUCERT para la notificación al cliente.

SET DEFINE OFF 
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET(
  ID_PARAMETRO_DET,
  PARAMETRO_ID,
  DESCRIPCION,
  VALOR1,
  VALOR2,
  VALOR3,
  VALOR4,
  ESTADO,
  USR_CREACION,FE_CREACION,IP_CREACION,USR_ULT_MOD,FE_ULT_MOD,IP_ULT_MOD,EMPRESA_COD) 
VALUES (
  DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
  276,
  'BASE DE PLANTILLA ECUCERT',
  '<p><strong>NOTIFICACI&Oacute;N DE IP GENERADORA DE strNombreCategoria</strong><br />======================================================</p>
  <p>Estimado Cliente,</p>

  <p>Le informamos que la ip {{ip}} por generar strNombreCategoria, raz&oacute;n por la cual solicitamos su justificaci&oacute;n del uso del puerto TCP 25 (SMTP). La fecha de detecci&oacute;n reportada es: {{timestamp}}.</p>
  <p>Una mala configuraci&oacute;n de este puerto, as&iacute; como la infecci&oacute;n con alg&uacute;n tipo de virus o malware de uno o m&aacute;s equipos de su red interna, que hagan uso de la IP reportada para su salida a internet, puede dar paso al env&iacute;o masivo de correos electr&oacute;nicos no deseados a trav&eacute;s del equipo o la red afectada.</p>

  <p>El enlistamiento de la IP puede ser verificado en el siguiente enlace: [http://www.uceprotect.net/en/rblcheck.php]</p>

  <p>Netlife, con el fin de precautelar la disponibilidad e integridad de su servicio, el de nuestros<br />clientes y el de nuestra infraestructura, solicita su colaboraci&oacute;n revisando los equipos de red interna en busca de<br />malware que pudiera estar comprometiendo al equipo, y desinfectarlo completamente. En caso de<br />desconocer si el equipo est&aacute; infectado, recomendamos reinstalarlo completamente; adem&aacute;s de<br />mantener siempre el equipo con un antivirus actualizado. Si existe una red interna que sale a trav&eacute;s de la IP ofensora, todos los equipos de esta red deben ser revisados, puesto, muy probablemente, varios de los equipos de esta red se encuentren<br />infectados.</p>
  <p>Si la IP reportada pertenece a un servidor de correos, se deben revisar las medidas antiSPAM implementadas en el mismo, a fin de evitar el env&Atilde;&shy;o de este tipo de correos.</p>

  <p>Quedamos a la espera de su pronta respuesta, y agradecemos la atenci&oacute;n prestada a la presente.</p>
  <p>En caso de tener dudas, favor dirigir sus respuestas al correo strCorreo.</p>
  <p>Atentamente,</p>
  <p>Soporte T&eacute;cnico<br />strEmpresa</p>',
  'soporte@netlife.net.ec',
  'Netlife',
  'encargados_seguridad@netlife.net.ec',
  'Activo', 'nnaulal', SYSDATE, '127.0.0.1', 'nnaulal', SYSDATE, '127.0.0.1',18);

COMMIT;
/
