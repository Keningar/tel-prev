/** 
 * @author Alex Arreaga <atarreaga@telconet.ec>
 * @version 1.0 
 * @since 15-02-2023
 * Se crea DML de configuraciones Ecuanet.
 */
 
---DB_FINANCIERO---

--OBSERVACION_FACTURA_MANUAL
Insert into DB_GENERAL.ADMI_PARAMETRO_DET (ID_PARAMETRO_DET,PARAMETRO_ID,DESCRIPCION,VALOR1,VALOR2,VALOR3,VALOR4,ESTADO,USR_CREACION,FE_CREACION,IP_CREACION,USR_ULT_MOD,FE_ULT_MOD,IP_ULT_MOD,VALOR5,EMPRESA_COD,VALOR6,VALOR7,OBSERVACION,VALOR8,VALOR9) 
values (DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,(SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'OBSERVACION_FACTURA_MANUAL'),'MUESTRA CAMPO OBSERVACION EN NUEVA FACTURA MANUAL EN ECUANET','EN','N',null,null,'Activo','atarreaga',sysdate,'127.0.0.1',null,null,null,null,'33',null,null,null,null,null);

--OPCIONES_HABILITADAS_FINANCIERO
Insert into DB_GENERAL.ADMI_PARAMETRO_DET (ID_PARAMETRO_DET,PARAMETRO_ID,DESCRIPCION,VALOR1,VALOR2,VALOR3,VALOR4,ESTADO,USR_CREACION,FE_CREACION,IP_CREACION,USR_ULT_MOD,FE_ULT_MOD,IP_ULT_MOD,VALOR5,EMPRESA_COD,VALOR6,VALOR7,OBSERVACION,VALOR8,VALOR9) 
values (DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,(SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'OPCIONES_HABILITADAS_FINANCIERO'),'FAC','FECHA_CONSUMO','S','NULL','NULL','Activo','atarreaga',sysdate,'127.0.0.1',null,null,null,'NULL','33',null,null,null,null,null);

--Proceso FACTURAS PROPORCIONALES. --PERIODO_FACTURACION
Insert into DB_GENERAL.ADMI_PARAMETRO_DET (ID_PARAMETRO_DET,PARAMETRO_ID,DESCRIPCION,VALOR1,VALOR2,VALOR3,VALOR4,ESTADO,USR_CREACION,FE_CREACION,IP_CREACION,USR_ULT_MOD,FE_ULT_MOD,IP_ULT_MOD,VALOR5,EMPRESA_COD,VALOR6,VALOR7,OBSERVACION,VALOR8,VALOR9) 
values (DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,384,'PERIODO DE FACTURACION EN','01',null,null,null,'Activo','atarreaga',sysdate,'127.0.0.1',null,null,null,null,'33',null,null,null,null,null);

--SECUENCIALES_POR_EMPRESA
Insert into DB_GENERAL.ADMI_PARAMETRO_DET (ID_PARAMETRO_DET,PARAMETRO_ID,DESCRIPCION,VALOR1,VALOR2,VALOR3,VALOR4,ESTADO,USR_CREACION,FE_CREACION,IP_CREACION,USR_ULT_MOD,FE_ULT_MOD,IP_ULT_MOD,VALOR5,EMPRESA_COD,VALOR6,VALOR7,OBSERVACION,VALOR8,VALOR9) 
values (DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,(SELECT ID_PARAMETRO FROM DB_GENERAL.admi_parametro_cab WHERE nombre_parametro = 'SECUENCIALES_POR_EMPRESA'),'FAC','9','NULL','NULL','NULL','Activo','atarreaga',sysdate,'127.0.0.1',null,null,null,'NULL','33',null,null,null,null,null);

COMMIT;


---DB_COMPROBANTES---

--ADMI_EMPRESA
SET DEFINE OFF;
Insert into DB_COMPROBANTES.ADMI_EMPRESA (ID_EMPRESA,CODIGO,NOMBRE,FE_CREACION,USR_CREACION,FE_ULT_MOD,USR_ULT_MOD,IP_CREACION,RUC,CORREO,DIRECCION,AMBIENTE_ID,LOTEMASIVO,CONTACTO,MARCA,PAGINA,CORREO_CONTACTO) 
values (DB_COMPROBANTES.SEQ_ADMI_EMPRESA.NEXTVAL,'EN','ECUANET S.A.',sysdate,'atarreaga',null,null,'127.0.0.1','1791287542001','facturacionelectronica@ecuanet.com.ec','Matriz Quito: Nu'||chr(38)||'ntilde;ez de Vela E3-13 y Atahualpa. Edificio torre del Puente 2do Piso | Sucursal Guayaquil: Av. Rodrigo de Ch'||chr(38)||'aacute;vez Parque Empresarial Col'||chr(38)||'oacute;n Edif. ColonCorp Torre 6 Locales comerciales 4 y 5','2','0','1-700 ECUANET | 3731300','Ecuanet','http://netlife.ec/','cobranzas@ecuanet.ec');

COMMIT;


--ADMI_EMPRESA_NOTICIA
SET DEFINE OFF;
Insert into DB_COMPROBANTES.ADMI_EMPRESA_NOTICIA (ID_EMPRESA_NOTICIA,EMPRESA_ID,TITULO,DETALLE,FECHA,ESTADO,FE_CREACION,USR_CREACION,FE_ULT_MOD,USR_ULT_MOD,IP_CREACION) 
values (DB_COMPROBANTES.SEQ_ADMI_EMPRESA_NOTICIA.NEXTVAL,
(SELECT ID_EMPRESA FROM DB_COMPROBANTES.ADMI_EMPRESA WHERE CODIGO = 'EN'),
'Portada Ecuanet',
'<div style="width: 100%;">
<h3>Facturación Electrónica ECUANET</h3>
<div style="text-align:right; width:100%;">
<img style="height: 200px;" src="/resources/2/31118.jpg">
</div>
<p style="text-align: justify;">Nuestro portal cuenta con funcionalidades como b'||chr(38)||'uacute;squeda de facturas por varios criterios y generación de reportes de auditor'||chr(38)||'iacute;a. Integra firmas electrónicas de entidades de certificación avaladas en Ecuador, permitiendo validar la autenticidad e integridad de los comprobantes electrónicos.</p>
<p><b>Comprobantes Soportados:</b></p>
<ul>
<li>Facturas</li>
<li>Notas de crédito</li>
<li>Notas de débito</li>
<li>Comprobantes de retención</li>
<li>Guías de remisión</li>
</ul>
</div>',sysdate,'Activo',sysdate,'atarreaga',null,null,'127.0.0.1');

--ADMI_EMPRESA_PARAMETRO
SET DEFINE OFF;
Insert into DB_COMPROBANTES.ADMI_EMPRESA_PARAMETRO (ID_EMPRESA_PARAMETRO,EMPRESA_ID,CLAVE,VALOR,DESCRIPCION,ES_CONFIG,ES_DEFAULT,FE_CREACION,USR_CREACION,FE_ULT_MOD,USR_ULT_MOD,IP_CREACION) values (DB_COMPROBANTES.SEQ_ADMI_EMPRESA_PARAMETRO.NEXTVAL,(SELECT ID_EMPRESA FROM DB_COMPROBANTES.ADMI_EMPRESA WHERE CODIGO = 'EN'),'Bienvenida','Bienvenidos a portal de comprobantes electr'||chr(38)||'oacute;nicos de Ecuanet',null,'N','N',sysdate,'atarreaga',null,null,'127.0.0.1');
Insert into DB_COMPROBANTES.ADMI_EMPRESA_PARAMETRO (ID_EMPRESA_PARAMETRO,EMPRESA_ID,CLAVE,VALOR,DESCRIPCION,ES_CONFIG,ES_DEFAULT,FE_CREACION,USR_CREACION,FE_ULT_MOD,USR_ULT_MOD,IP_CREACION) values (DB_COMPROBANTES.SEQ_ADMI_EMPRESA_PARAMETRO.NEXTVAL,(SELECT ID_EMPRESA FROM DB_COMPROBANTES.ADMI_EMPRESA WHERE CODIGO = 'EN'),'NombreWifi','Ecuanet - Comprobantes Electrónicos',null,'N','N',sysdate,'atarreaga',null,null,'127.0.0.1');
Insert into DB_COMPROBANTES.ADMI_EMPRESA_PARAMETRO (ID_EMPRESA_PARAMETRO,EMPRESA_ID,CLAVE,VALOR,DESCRIPCION,ES_CONFIG,ES_DEFAULT,FE_CREACION,USR_CREACION,FE_ULT_MOD,USR_ULT_MOD,IP_CREACION) values (DB_COMPROBANTES.SEQ_ADMI_EMPRESA_PARAMETRO.NEXTVAL,(SELECT ID_EMPRESA FROM DB_COMPROBANTES.ADMI_EMPRESA WHERE CODIGO = 'EN'),'LogoTop','logo.png',null,'N','N',sysdate,'atarreaga',null,null,'127.0.0.1');
Insert into DB_COMPROBANTES.ADMI_EMPRESA_PARAMETRO (ID_EMPRESA_PARAMETRO,EMPRESA_ID,CLAVE,VALOR,DESCRIPCION,ES_CONFIG,ES_DEFAULT,FE_CREACION,USR_CREACION,FE_ULT_MOD,USR_ULT_MOD,IP_CREACION) values (DB_COMPROBANTES.SEQ_ADMI_EMPRESA_PARAMETRO.NEXTVAL,(SELECT ID_EMPRESA FROM DB_COMPROBANTES.ADMI_EMPRESA WHERE CODIGO = 'EN'),'DirImagenes','/firma/images',null,'N','N',sysdate,'atarreaga',null,null,'127.0.0.1');
Insert into DB_COMPROBANTES.ADMI_EMPRESA_PARAMETRO (ID_EMPRESA_PARAMETRO,EMPRESA_ID,CLAVE,VALOR,DESCRIPCION,ES_CONFIG,ES_DEFAULT,FE_CREACION,USR_CREACION,FE_ULT_MOD,USR_ULT_MOD,IP_CREACION) values (DB_COMPROBANTES.SEQ_ADMI_EMPRESA_PARAMETRO.NEXTVAL,(SELECT ID_EMPRESA FROM DB_COMPROBANTES.ADMI_EMPRESA WHERE CODIGO = 'EN'),'encuesta_requerida_login','N','Parametro que permite indicar si es necesario o no que un Usuario llene una encuesta habilitada antes de iniciar sesi'||chr(38)||'oacute;n','N','S',sysdate,'atarreaga',null,null,'127.0.0.1');
Insert into DB_COMPROBANTES.ADMI_EMPRESA_PARAMETRO (ID_EMPRESA_PARAMETRO,EMPRESA_ID,CLAVE,VALOR,DESCRIPCION,ES_CONFIG,ES_DEFAULT,FE_CREACION,USR_CREACION,FE_ULT_MOD,USR_ULT_MOD,IP_CREACION) values (DB_COMPROBANTES.SEQ_ADMI_EMPRESA_PARAMETRO.NEXTVAL,(SELECT ID_EMPRESA FROM DB_COMPROBANTES.ADMI_EMPRESA WHERE CODIGO = 'EN'),'ServerName','https://facturacion.telconet.net/',null,'S','N',sysdate,'atarreaga',null,null,'127.0.0.1');
Insert into DB_COMPROBANTES.ADMI_EMPRESA_PARAMETRO (ID_EMPRESA_PARAMETRO,EMPRESA_ID,CLAVE,VALOR,DESCRIPCION,ES_CONFIG,ES_DEFAULT,FE_CREACION,USR_CREACION,FE_ULT_MOD,USR_ULT_MOD,IP_CREACION) values (DB_COMPROBANTES.SEQ_ADMI_EMPRESA_PARAMETRO.NEXTVAL,(SELECT ID_EMPRESA FROM DB_COMPROBANTES.ADMI_EMPRESA WHERE CODIGO = 'EN'),'auth_registro','S','Permitir registro para el acceso de los usuario','N','S',sysdate,'atarreaga',null,null,'127.0.0.1');
Insert into DB_COMPROBANTES.ADMI_EMPRESA_PARAMETRO (ID_EMPRESA_PARAMETRO,EMPRESA_ID,CLAVE,VALOR,DESCRIPCION,ES_CONFIG,ES_DEFAULT,FE_CREACION,USR_CREACION,FE_ULT_MOD,USR_ULT_MOD,IP_CREACION) values (DB_COMPROBANTES.SEQ_ADMI_EMPRESA_PARAMETRO.NEXTVAL,(SELECT ID_EMPRESA FROM DB_COMPROBANTES.ADMI_EMPRESA WHERE CODIGO = 'EN'),'ColorUno','0026ff',null,'N','N',sysdate,'atarreaga',null,null,'127.0.0.1');
Insert into DB_COMPROBANTES.ADMI_EMPRESA_PARAMETRO (ID_EMPRESA_PARAMETRO,EMPRESA_ID,CLAVE,VALOR,DESCRIPCION,ES_CONFIG,ES_DEFAULT,FE_CREACION,USR_CREACION,FE_ULT_MOD,USR_ULT_MOD,IP_CREACION) values (DB_COMPROBANTES.SEQ_ADMI_EMPRESA_PARAMETRO.NEXTVAL,(SELECT ID_EMPRESA FROM DB_COMPROBANTES.ADMI_EMPRESA WHERE CODIGO = 'EN'),'Protocol','https',null,'S','N',sysdate,'atarreaga',null,null,'127.0.0.1');
Insert into DB_COMPROBANTES.ADMI_EMPRESA_PARAMETRO (ID_EMPRESA_PARAMETRO,EMPRESA_ID,CLAVE,VALOR,DESCRIPCION,ES_CONFIG,ES_DEFAULT,FE_CREACION,USR_CREACION,FE_ULT_MOD,USR_ULT_MOD,IP_CREACION) values (DB_COMPROBANTES.SEQ_ADMI_EMPRESA_PARAMETRO.NEXTVAL,(SELECT ID_EMPRESA FROM DB_COMPROBANTES.ADMI_EMPRESA WHERE CODIGO = 'EN'),'SSID','ECUANET_SSID',null,'S','N',sysdate,'atarreaga',null,null,'127.0.0.1');
Insert into DB_COMPROBANTES.ADMI_EMPRESA_PARAMETRO (ID_EMPRESA_PARAMETRO,EMPRESA_ID,CLAVE,VALOR,DESCRIPCION,ES_CONFIG,ES_DEFAULT,FE_CREACION,USR_CREACION,FE_ULT_MOD,USR_ULT_MOD,IP_CREACION) values (DB_COMPROBANTES.SEQ_ADMI_EMPRESA_PARAMETRO.NEXTVAL,(SELECT ID_EMPRESA FROM DB_COMPROBANTES.ADMI_EMPRESA WHERE CODIGO = 'EN'),'Dominio','facturacion.telconet.net',null,'S','N',sysdate,'atarreaga',null,null,'127.0.0.1');
Insert into DB_COMPROBANTES.ADMI_EMPRESA_PARAMETRO (ID_EMPRESA_PARAMETRO,EMPRESA_ID,CLAVE,VALOR,DESCRIPCION,ES_CONFIG,ES_DEFAULT,FE_CREACION,USR_CREACION,FE_ULT_MOD,USR_ULT_MOD,IP_CREACION) values (DB_COMPROBANTES.SEQ_ADMI_EMPRESA_PARAMETRO.NEXTVAL,(SELECT ID_EMPRESA FROM DB_COMPROBANTES.ADMI_EMPRESA WHERE CODIGO = 'EN'),'auth_login','compuesto','Modo de acceso de los usuario','N','S',sysdate,'atarreaga',null,null,'127.0.0.1');
Insert into DB_COMPROBANTES.ADMI_EMPRESA_PARAMETRO (ID_EMPRESA_PARAMETRO,EMPRESA_ID,CLAVE,VALOR,DESCRIPCION,ES_CONFIG,ES_DEFAULT,FE_CREACION,USR_CREACION,FE_ULT_MOD,USR_ULT_MOD,IP_CREACION) values (DB_COMPROBANTES.SEQ_ADMI_EMPRESA_PARAMETRO.NEXTVAL,(SELECT ID_EMPRESA FROM DB_COMPROBANTES.ADMI_EMPRESA WHERE CODIGO = 'EN'),'tema','start',null,'N','N',sysdate,'atarreaga',null,null,'127.0.0.1');
Insert into DB_COMPROBANTES.ADMI_EMPRESA_PARAMETRO (ID_EMPRESA_PARAMETRO,EMPRESA_ID,CLAVE,VALOR,DESCRIPCION,ES_CONFIG,ES_DEFAULT,FE_CREACION,USR_CREACION,FE_ULT_MOD,USR_ULT_MOD,IP_CREACION) values (DB_COMPROBANTES.SEQ_ADMI_EMPRESA_PARAMETRO.NEXTVAL,(SELECT ID_EMPRESA FROM DB_COMPROBANTES.ADMI_EMPRESA WHERE CODIGO = 'EN'),'Puerto','443',null,'N','N',sysdate,'atarreaga',null,null,'127.0.0.1');
Insert into DB_COMPROBANTES.ADMI_EMPRESA_PARAMETRO (ID_EMPRESA_PARAMETRO,EMPRESA_ID,CLAVE,VALOR,DESCRIPCION,ES_CONFIG,ES_DEFAULT,FE_CREACION,USR_CREACION,FE_ULT_MOD,USR_ULT_MOD,IP_CREACION) values (DB_COMPROBANTES.SEQ_ADMI_EMPRESA_PARAMETRO.NEXTVAL,(SELECT ID_EMPRESA FROM DB_COMPROBANTES.ADMI_EMPRESA WHERE CODIGO = 'EN'),'systemSignature','Telconet © 2015. Todos los derechos reservados.',null,'N','N',sysdate,'atarreaga',null,null,'127.0.0.1');
Insert into DB_COMPROBANTES.ADMI_EMPRESA_PARAMETRO (ID_EMPRESA_PARAMETRO,EMPRESA_ID,CLAVE,VALOR,DESCRIPCION,ES_CONFIG,ES_DEFAULT,FE_CREACION,USR_CREACION,FE_ULT_MOD,USR_ULT_MOD,IP_CREACION) values (DB_COMPROBANTES.SEQ_ADMI_EMPRESA_PARAMETRO.NEXTVAL,(SELECT ID_EMPRESA FROM DB_COMPROBANTES.ADMI_EMPRESA WHERE CODIGO = 'EN'),'systemRights','Desarrollado por Telconet',null,'N','N',sysdate,'atarreaga',null,null,'127.0.0.1');
Insert into DB_COMPROBANTES.ADMI_EMPRESA_PARAMETRO (ID_EMPRESA_PARAMETRO,EMPRESA_ID,CLAVE,VALOR,DESCRIPCION,ES_CONFIG,ES_DEFAULT,FE_CREACION,USR_CREACION,FE_ULT_MOD,USR_ULT_MOD,IP_CREACION) values (DB_COMPROBANTES.SEQ_ADMI_EMPRESA_PARAMETRO.NEXTVAL,(SELECT ID_EMPRESA FROM DB_COMPROBANTES.ADMI_EMPRESA WHERE CODIGO = 'EN'),'validar_anulacion_sri','N','Validación con WS del SRI para la anulación','S','N',sysdate,'atarreaga',null,null,'127.0.0.1');
Insert into DB_COMPROBANTES.ADMI_EMPRESA_PARAMETRO (ID_EMPRESA_PARAMETRO,EMPRESA_ID,CLAVE,VALOR,DESCRIPCION,ES_CONFIG,ES_DEFAULT,FE_CREACION,USR_CREACION,FE_ULT_MOD,USR_ULT_MOD,IP_CREACION) values (DB_COMPROBANTES.SEQ_ADMI_EMPRESA_PARAMETRO.NEXTVAL,(SELECT ID_EMPRESA FROM DB_COMPROBANTES.ADMI_EMPRESA WHERE CODIGO = 'EN'),'DirRecursos','/resources','Ruta donde se encuentra los recursos en el apache, por ejemplo imagenes de la empresa','N','N',sysdate,'atarreaga',null,null,'127.0.0.1');

--ADMI_PARAMETRO_DET
Insert into DB_COMPROBANTES.ADMI_PARAMETRO_DET (ID_PARAMETRO_DET,PARAMETRO_ID,DESCRIPCION,VALOR1,VALOR2,VALOR3,VALOR4,ESTADO,USR_CREACION,FE_CREACION,IP_CREACION,USR_ULT_MOD,FE_ULT_MOD,IP_ULT_MOD,VALOR5,EMPRESA_ID) values (DB_COMPROBANTES.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,'4','Cantidad en Horas para sumar al sysdate y generar la fecha de expiracion del token','TIEMPO_EXPIRACION_TOKEN','24','horas',null,'Activo','atarreaga',sysdate,'127.0.0.1',null,null,null,null,(SELECT ID_EMPRESA FROM DB_COMPROBANTES.ADMI_EMPRESA WHERE CODIGO = 'EN'));
Insert into DB_COMPROBANTES.ADMI_PARAMETRO_DET (ID_PARAMETRO_DET,PARAMETRO_ID,DESCRIPCION,VALOR1,VALOR2,VALOR3,VALOR4,ESTADO,USR_CREACION,FE_CREACION,IP_CREACION,USR_ULT_MOD,FE_ULT_MOD,IP_ULT_MOD,VALOR5,EMPRESA_ID) values (DB_COMPROBANTES.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,'5','Mensaje añadido al cuerpo del correo para la siguiente forma de pago','DEBITO BANCARIO','<br/><p style="font-family: Arial; font-size: 11px; font-style: normal; font-variant: normal; font-weight: normal; line-height: normal;">Le recordamos que su forma de pago contractual es mediante d'||chr(38)||'eacute;bito bancario que se ejecuta el primer d'||chr(38)||'iacute;a h'||chr(38)||'aacute;bil de cada mes, en caso de que el d'||chr(38)||'eacute;bito sea rechazado por cualquier motivo, se proceder'||chr(38)||'aacute; a facturar el recargo de $1,00 (m'||chr(38)||'aacute;s impuestos) por gesti'||chr(38)||'oacute;n de cobranza y usted lo ver'||chr(38)||'aacute; reflejado en su pr'||chr(38)||'oacute;xima factura cumpliendo con la cl'||chr(38)||'aacute;usula estipulada en su contrato de servicios.</p><p><font face="Arial"><span style="font-size: 11px;">Para consultar nuestros canales de recaudaci'||chr(38)||'oacute;n usted puede ingresar al siguiente link: </span></font><span style="font-family: Arial; font-size: 11px;"><a href="http://www.netlife.ec/atencion-al-cliente/#formas_de_pago" >http://www.netlife.ec/atencion-al-cliente/#formas_de_pago</a></span></p><br/>','NULL','NULL','Activo','atarreaga',sysdate,'127.0.0.1',null,null,null,null,(SELECT ID_EMPRESA FROM DB_COMPROBANTES.ADMI_EMPRESA WHERE CODIGO = 'EN'));
Insert into DB_COMPROBANTES.ADMI_PARAMETRO_DET (ID_PARAMETRO_DET,PARAMETRO_ID,DESCRIPCION,VALOR1,VALOR2,VALOR3,VALOR4,ESTADO,USR_CREACION,FE_CREACION,IP_CREACION,USR_ULT_MOD,FE_ULT_MOD,IP_ULT_MOD,VALOR5,EMPRESA_ID) values (DB_COMPROBANTES.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,'5','Mensaje añadido al cuerpo del correo para la siguiente forma de pago','TARJETA DE CREDITO','<br/><p style="font-family: Arial; font-size: 11px; font-style: normal; font-variant: normal; font-weight: normal; line-height: normal;">Le recordamos que su forma de pago contractual es mediante d'||chr(38)||'eacute;bito bancario que se ejecuta el primer d'||chr(38)||'iacute;a h'||chr(38)||'aacute;bil de cada mes, en caso de que el d'||chr(38)||'eacute;bito sea rechazado por cualquier motivo, se proceder'||chr(38)||'aacute; a facturar el recargo de $1,00 (m'||chr(38)||'aacute;s impuestos) por gesti'||chr(38)||'oacute;n de cobranza y usted lo ver'||chr(38)||'aacute; reflejado en su pr'||chr(38)||'oacute;xima factura cumpliendo con la cl'||chr(38)||'aacute;usula estipulada en su contrato de servicios.</p><p><font face="Arial"><span style="font-size: 11px;">Para consultar nuestros canales de recaudaci'||chr(38)||'oacute;n usted puede ingresar al siguiente link: </span></font><span style="font-family: Arial; font-size: 11px;"><a href="http://www.netlife.ec/atencion-al-cliente/#formas_de_pago" >http://www.netlife.ec/atencion-al-cliente/#formas_de_pago</a></span></p><br/>','NULL','NULL','Activo','atarreaga',sysdate,'127.0.0.1',null,null,null,null,(SELECT ID_EMPRESA FROM DB_COMPROBANTES.ADMI_EMPRESA WHERE CODIGO = 'EN'));
Insert into DB_COMPROBANTES.ADMI_PARAMETRO_DET (ID_PARAMETRO_DET,PARAMETRO_ID,DESCRIPCION,VALOR1,VALOR2,VALOR3,VALOR4,ESTADO,USR_CREACION,FE_CREACION,IP_CREACION,USR_ULT_MOD,FE_ULT_MOD,IP_ULT_MOD,VALOR5,EMPRESA_ID) values (DB_COMPROBANTES.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,'6','CAMBIO DE ESTADO NO PERMITIDO PARA LOS DOCUMENTOS FINANCIEROS DE EN','FAC','8','5','EN','Activo','atarreaga',sysdate,'127.0.0.1',null,null,null,'NULL',(SELECT ID_EMPRESA FROM DB_COMPROBANTES.ADMI_EMPRESA WHERE CODIGO = 'EN'));
Insert into DB_COMPROBANTES.ADMI_PARAMETRO_DET (ID_PARAMETRO_DET,PARAMETRO_ID,DESCRIPCION,VALOR1,VALOR2,VALOR3,VALOR4,ESTADO,USR_CREACION,FE_CREACION,IP_CREACION,USR_ULT_MOD,FE_ULT_MOD,IP_ULT_MOD,VALOR5,EMPRESA_ID) values (DB_COMPROBANTES.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,'6','CAMBIO DE ESTADO NO PERMITIDO PARA LOS DOCUMENTOS FINANCIEROS DE EN','FAC','5','1','EN','Activo','atarreaga',sysdate,'127.0.0.1',null,null,null,'NULL',(SELECT ID_EMPRESA FROM DB_COMPROBANTES.ADMI_EMPRESA WHERE CODIGO = 'EN'));
Insert into DB_COMPROBANTES.ADMI_PARAMETRO_DET (ID_PARAMETRO_DET,PARAMETRO_ID,DESCRIPCION,VALOR1,VALOR2,VALOR3,VALOR4,ESTADO,USR_CREACION,FE_CREACION,IP_CREACION,USR_ULT_MOD,FE_ULT_MOD,IP_ULT_MOD,VALOR5,EMPRESA_ID) values (DB_COMPROBANTES.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,'6','CAMBIO DE ESTADO NO PERMITIDO PARA LOS DOCUMENTOS FINANCIEROS DE EN','FAC','2','1','EN','Activo','atarreaga',sysdate,'127.0.0.1',null,null,null,'NULL',(SELECT ID_EMPRESA FROM DB_COMPROBANTES.ADMI_EMPRESA WHERE CODIGO = 'EN'));
Insert into DB_COMPROBANTES.ADMI_PARAMETRO_DET (ID_PARAMETRO_DET,PARAMETRO_ID,DESCRIPCION,VALOR1,VALOR2,VALOR3,VALOR4,ESTADO,USR_CREACION,FE_CREACION,IP_CREACION,USR_ULT_MOD,FE_ULT_MOD,IP_ULT_MOD,VALOR5,EMPRESA_ID) values (DB_COMPROBANTES.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,'7','ECUANET','EN','N',null,null,'Activo','atarreaga',sysdate,'127.0.0.1',null,null,null,null,(SELECT ID_EMPRESA FROM DB_COMPROBANTES.ADMI_EMPRESA WHERE CODIGO = 'EN'));
Insert into DB_COMPROBANTES.ADMI_PARAMETRO_DET (ID_PARAMETRO_DET,PARAMETRO_ID,DESCRIPCION,VALOR1,VALOR2,VALOR3,VALOR4,ESTADO,USR_CREACION,FE_CREACION,IP_CREACION,USR_ULT_MOD,FE_ULT_MOD,IP_ULT_MOD,VALOR5,EMPRESA_ID) values (DB_COMPROBANTES.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,'6','CAMBIO DE ESTADO NO PERMITIDO PARA LOS DOCUMENTOS FINANCIEROS DE EN','FAC','5','2','EN','Activo','atarreaga',sysdate,'127.0.0.1',null,null,null,'NULL',(SELECT ID_EMPRESA FROM DB_COMPROBANTES.ADMI_EMPRESA WHERE CODIGO = 'EN'));
Insert into DB_COMPROBANTES.ADMI_PARAMETRO_DET (ID_PARAMETRO_DET,PARAMETRO_ID,DESCRIPCION,VALOR1,VALOR2,VALOR3,VALOR4,ESTADO,USR_CREACION,FE_CREACION,IP_CREACION,USR_ULT_MOD,FE_ULT_MOD,IP_ULT_MOD,VALOR5,EMPRESA_ID) values (DB_COMPROBANTES.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,'8','Detalles del parametro para PLANES. Envio de email en comprobantes electronicos','^(?=.*\bHOGAR\b)(?=.*50).*$','<p>Oferta Exclusiva de Ecuanet. 20% m'||chr(38)||'aacute;s velocidad + 3 licencias de seguridad inform'||chr(38)||'aacute;tica + WiFi PRO alta estabilidad x tan s'||chr(38)||'oacute;lo $1,01+imp adicional al mes, para acceder a esta promoci'||chr(38)||'oacute;n dar click en el siguiente link. Aplica condiciones y restricciones <br/> <a href="https://www.netlife.ec/actualiza-tu-plan/?upgrade=60">https://www.netlife.ec/actualiza-tu-plan/?upgrade=60</a></p>','PLANES','EN','Activo','atarreaga',to_date('27/12/2018','DD/MM/RRRR'),'127.0.0.1',null,null,null,null,(SELECT ID_EMPRESA FROM DB_COMPROBANTES.ADMI_EMPRESA WHERE CODIGO = 'EN'));
Insert into DB_COMPROBANTES.ADMI_PARAMETRO_DET (ID_PARAMETRO_DET,PARAMETRO_ID,DESCRIPCION,VALOR1,VALOR2,VALOR3,VALOR4,ESTADO,USR_CREACION,FE_CREACION,IP_CREACION,USR_ULT_MOD,FE_ULT_MOD,IP_ULT_MOD,VALOR5,EMPRESA_ID) values (DB_COMPROBANTES.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,'8','Detalles del parametro para FORMA_PAGO. Envio de email en comprobantes electronicos','\bDEBITO\b|\bEFECTIVO\b|\bCHEQUE\b','<p>Oferta Exclusiva de Ecuanet. 20% m'||chr(38)||'aacute;s velocidad + 3 licencias de seguridad inform'||chr(38)||'aacute;tica + WiFi PRO alta estabilidad x tan s'||chr(38)||'oacute;lo $1,01+imp adicional al mes, para acceder a esta promoci'||chr(38)||'oacute;n dar click en el siguiente link. Aplica condiciones y restricciones <br/> <a href="https://www.netlife.ec/actualiza-tu-plan/?upgrade=60">https://www.netlife.ec/actualiza-tu-plan/?upgrade=60</a></p>','FORMA_PAGO','EN','Inactivo','atarreaga',sysdate,'127.0.0.1',null,null,null,null,(SELECT ID_EMPRESA FROM DB_COMPROBANTES.ADMI_EMPRESA WHERE CODIGO = 'EN'));
Insert into DB_COMPROBANTES.ADMI_PARAMETRO_DET (ID_PARAMETRO_DET,PARAMETRO_ID,DESCRIPCION,VALOR1,VALOR2,VALOR3,VALOR4,ESTADO,USR_CREACION,FE_CREACION,IP_CREACION,USR_ULT_MOD,FE_ULT_MOD,IP_ULT_MOD,VALOR5,EMPRESA_ID) values (DB_COMPROBANTES.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,'9','CORREO GENERAL PARA NOTIFICACION DE GUIA DE REMISION','guias_remision@netlife.net.ec',null,'CORREO','EN','Activo','atarreaga',sysdate,'127.0.0.1',null,null,null,'NULL',(SELECT ID_EMPRESA FROM DB_COMPROBANTES.ADMI_EMPRESA WHERE CODIGO = 'EN'));
Insert into DB_COMPROBANTES.ADMI_PARAMETRO_DET (ID_PARAMETRO_DET,PARAMETRO_ID,DESCRIPCION,VALOR1,VALOR2,VALOR3,VALOR4,ESTADO,USR_CREACION,FE_CREACION,IP_CREACION,USR_ULT_MOD,FE_ULT_MOD,IP_ULT_MOD,VALOR5,EMPRESA_ID) values (DB_COMPROBANTES.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,'16','Detalles del parametro para plantilla mejorada EN. Envio de email en comprobantes electronicos','S','EN','VALIDA_PLANTILLA','3.0.0','Activo','atarreaga',sysdate,'127.0.0.1',null,null,null,'Revisa tu Documento Electrónico de ECUANET',(SELECT ID_EMPRESA FROM DB_COMPROBANTES.ADMI_EMPRESA WHERE CODIGO = 'EN'));


--ADMI_USUARIO
Insert into DB_COMPROBANTES.ADMI_USUARIO (ID_USUARIO,LOGIN,NOMBRES,APELLIDOS,EMAIL,ADMIN,ESTADO,FE_CREACION,USR_CREACION,FE_ULT_MOD,USR_ULT_MOD,IP_CREACION,PASSWORD,EMPRESA,LOCALE,EMPRESA_CONSULTA) 
values (DB_COMPROBANTES.SEQ_ADMI_USUARIO.NEXTVAL,'ecuanet','ECUA','NET','info@megadatos.ec','N','Activo',sysdate,'atarreaga',sysdate,'atarreaga','127.0.0.1','945effc0a8bd20467190ff7605b7a5b92648b9e4b8e2b4b64da5f12fbb86c70a','S','es_EC','N');

COMMIT;

--ADMI_USUARIO_EMPRESA
Insert into DB_COMPROBANTES.ADMI_USUARIO_EMPRESA (ID_USR_EMP,USUARIO_ID,EMPRESA_ID,FE_CREACION,USR_CREACION,FE_ULT_MOD,USR_ULT_MOD,IP_CREACION,EMAIL,DIRECCION,TELEFONO,CIUDAD,NUMERO,FORMAPAGO,LOGIN,CONTRATO,PASSWORD,N_CONEXION,FE_ULT_CONEXION,CAMBIO_CLAVE) values (DB_COMPROBANTES.SEQ_ADMI_USUARIO_EMPRESA.NEXTVAL,(select id_usuario from DB_COMPROBANTES.ADMI_USUARIO where login = 'ecuanet'),(SELECT ID_EMPRESA FROM DB_COMPROBANTES.ADMI_EMPRESA WHERE CODIGO = 'EN'),sysdate,'atarreaga',null,null,'127.0.0.1',null,null,null,null,null,null,null,null,'945effc0a8bd20467190ff7605b7a5b92648b9e4b8e2b4b64da5f12fbb86c70a','574',sysdate,'N');


--INFO_CAMPO_ADICIONAL
SET DEFINE OFF;
Insert into DB_COMPROBANTES.INFO_CAMPO_ADICIONAL (ID_CAMPO_ADICIONAL,EMPRESA_ID,TIPO_DOC_ID,CODIGO,ETIQUETA,DESCRIPCION,POSICION,ESTADO,FE_CREACION,USR_CREACION,FE_ULT_MOD,USR_ULT_MOD,IP_CREACION) values (DB_COMPROBANTES.SEQ_INFO_CAMPO_ADICIONAL.NEXTVAL,(SELECT ID_EMPRESA FROM DB_COMPROBANTES.ADMI_EMPRESA WHERE CODIGO = 'EN'),'1','fmaxPago','Fecha Máxima de pago',null,'10','Activo',sysdate,'atarreaga',null,null,'0.0.0.0');
Insert into DB_COMPROBANTES.INFO_CAMPO_ADICIONAL (ID_CAMPO_ADICIONAL,EMPRESA_ID,TIPO_DOC_ID,CODIGO,ETIQUETA,DESCRIPCION,POSICION,ESTADO,FE_CREACION,USR_CREACION,FE_ULT_MOD,USR_ULT_MOD,IP_CREACION) values (DB_COMPROBANTES.SEQ_INFO_CAMPO_ADICIONAL.NEXTVAL,(SELECT ID_EMPRESA FROM DB_COMPROBANTES.ADMI_EMPRESA WHERE CODIGO = 'EN'),'8','emailCliente','Email',null,'1','Activo',sysdate,'atarreaga',null,null,'127.0.0.1');
Insert into DB_COMPROBANTES.INFO_CAMPO_ADICIONAL (ID_CAMPO_ADICIONAL,EMPRESA_ID,TIPO_DOC_ID,CODIGO,ETIQUETA,DESCRIPCION,POSICION,ESTADO,FE_CREACION,USR_CREACION,FE_ULT_MOD,USR_ULT_MOD,IP_CREACION) values (DB_COMPROBANTES.SEQ_INFO_CAMPO_ADICIONAL.NEXTVAL,(SELECT ID_EMPRESA FROM DB_COMPROBANTES.ADMI_EMPRESA WHERE CODIGO = 'EN'),'2','pais','Pais',null,'4','Activo',sysdate,'atarreaga',null,null,'127.0.0.1');
Insert into DB_COMPROBANTES.INFO_CAMPO_ADICIONAL (ID_CAMPO_ADICIONAL,EMPRESA_ID,TIPO_DOC_ID,CODIGO,ETIQUETA,DESCRIPCION,POSICION,ESTADO,FE_CREACION,USR_CREACION,FE_ULT_MOD,USR_ULT_MOD,IP_CREACION) values (DB_COMPROBANTES.SEQ_INFO_CAMPO_ADICIONAL.NEXTVAL,(SELECT ID_EMPRESA FROM DB_COMPROBANTES.ADMI_EMPRESA WHERE CODIGO = 'EN'),'2','notificacion','Notificaci'||chr(38)||'oacute;n',null,'5','Activo',sysdate,'atarreaga',null,null,'127.0.0.1');
Insert into DB_COMPROBANTES.INFO_CAMPO_ADICIONAL (ID_CAMPO_ADICIONAL,EMPRESA_ID,TIPO_DOC_ID,CODIGO,ETIQUETA,DESCRIPCION,POSICION,ESTADO,FE_CREACION,USR_CREACION,FE_ULT_MOD,USR_ULT_MOD,IP_CREACION) values (DB_COMPROBANTES.SEQ_INFO_CAMPO_ADICIONAL.NEXTVAL,(SELECT ID_EMPRESA FROM DB_COMPROBANTES.ADMI_EMPRESA WHERE CODIGO = 'EN'),'2','emailCliente','Email',null,'4','Activo',sysdate,'atarreaga',null,null,'127.0.0.1');
Insert into DB_COMPROBANTES.INFO_CAMPO_ADICIONAL (ID_CAMPO_ADICIONAL,EMPRESA_ID,TIPO_DOC_ID,CODIGO,ETIQUETA,DESCRIPCION,POSICION,ESTADO,FE_CREACION,USR_CREACION,FE_ULT_MOD,USR_ULT_MOD,IP_CREACION) values (DB_COMPROBANTES.SEQ_INFO_CAMPO_ADICIONAL.NEXTVAL,(SELECT ID_EMPRESA FROM DB_COMPROBANTES.ADMI_EMPRESA WHERE CODIGO = 'EN'),'2','telfCliente','Tel'||chr(38)||'eacute;fono',null,'3','Activo',sysdate,'atarreaga',null,null,'127.0.0.1');
Insert into DB_COMPROBANTES.INFO_CAMPO_ADICIONAL (ID_CAMPO_ADICIONAL,EMPRESA_ID,TIPO_DOC_ID,CODIGO,ETIQUETA,DESCRIPCION,POSICION,ESTADO,FE_CREACION,USR_CREACION,FE_ULT_MOD,USR_ULT_MOD,IP_CREACION) values (DB_COMPROBANTES.SEQ_INFO_CAMPO_ADICIONAL.NEXTVAL,(SELECT ID_EMPRESA FROM DB_COMPROBANTES.ADMI_EMPRESA WHERE CODIGO = 'EN'),'2','dirCliente','Direcci'||chr(38)||'oacute;n',null,'2','Activo',sysdate,'atarreaga',null,null,'127.0.0.1');
Insert into DB_COMPROBANTES.INFO_CAMPO_ADICIONAL (ID_CAMPO_ADICIONAL,EMPRESA_ID,TIPO_DOC_ID,CODIGO,ETIQUETA,DESCRIPCION,POSICION,ESTADO,FE_CREACION,USR_CREACION,FE_ULT_MOD,USR_ULT_MOD,IP_CREACION) values (DB_COMPROBANTES.SEQ_INFO_CAMPO_ADICIONAL.NEXTVAL,(SELECT ID_EMPRESA FROM DB_COMPROBANTES.ADMI_EMPRESA WHERE CODIGO = 'EN'),'2','ciudadCliente','Ciudad',null,'1','Activo',sysdate,'atarreaga',null,null,'127.0.0.1');
Insert into DB_COMPROBANTES.INFO_CAMPO_ADICIONAL (ID_CAMPO_ADICIONAL,EMPRESA_ID,TIPO_DOC_ID,CODIGO,ETIQUETA,DESCRIPCION,POSICION,ESTADO,FE_CREACION,USR_CREACION,FE_ULT_MOD,USR_ULT_MOD,IP_CREACION) values (DB_COMPROBANTES.SEQ_INFO_CAMPO_ADICIONAL.NEXTVAL,(SELECT ID_EMPRESA FROM DB_COMPROBANTES.ADMI_EMPRESA WHERE CODIGO = 'EN'),'2','contribucionSolidaria','Compensaci'||chr(38)||'oacute;n Solidaria 2%',null,'6','Activo',sysdate,'atarreaga',null,null,'127.0.0.1');
Insert into DB_COMPROBANTES.INFO_CAMPO_ADICIONAL (ID_CAMPO_ADICIONAL,EMPRESA_ID,TIPO_DOC_ID,CODIGO,ETIQUETA,DESCRIPCION,POSICION,ESTADO,FE_CREACION,USR_CREACION,FE_ULT_MOD,USR_ULT_MOD,IP_CREACION) values (DB_COMPROBANTES.SEQ_INFO_CAMPO_ADICIONAL.NEXTVAL,(SELECT ID_EMPRESA FROM DB_COMPROBANTES.ADMI_EMPRESA WHERE CODIGO = 'EN'),'1','contribucionSolidaria','Compensaci'||chr(38)||'oacute;n Solidaria 2%',null,'9','Activo',sysdate,'atarreaga',null,null,'127.0.0.1');
Insert into DB_COMPROBANTES.INFO_CAMPO_ADICIONAL (ID_CAMPO_ADICIONAL,EMPRESA_ID,TIPO_DOC_ID,CODIGO,ETIQUETA,DESCRIPCION,POSICION,ESTADO,FE_CREACION,USR_CREACION,FE_ULT_MOD,USR_ULT_MOD,IP_CREACION) values (DB_COMPROBANTES.SEQ_INFO_CAMPO_ADICIONAL.NEXTVAL,(SELECT ID_EMPRESA FROM DB_COMPROBANTES.ADMI_EMPRESA WHERE CODIGO = 'EN'),'1','contratoCliente','Contrato',null,'8','Activo',sysdate,'atarreaga',null,null,'127.0.0.1');
Insert into DB_COMPROBANTES.INFO_CAMPO_ADICIONAL (ID_CAMPO_ADICIONAL,EMPRESA_ID,TIPO_DOC_ID,CODIGO,ETIQUETA,DESCRIPCION,POSICION,ESTADO,FE_CREACION,USR_CREACION,FE_ULT_MOD,USR_ULT_MOD,IP_CREACION) values (DB_COMPROBANTES.SEQ_INFO_CAMPO_ADICIONAL.NEXTVAL,(SELECT ID_EMPRESA FROM DB_COMPROBANTES.ADMI_EMPRESA WHERE CODIGO = 'EN'),'1','emailCliente','Email',null,'7','Activo',sysdate,'atarreaga',null,null,'127.0.0.1');
Insert into DB_COMPROBANTES.INFO_CAMPO_ADICIONAL (ID_CAMPO_ADICIONAL,EMPRESA_ID,TIPO_DOC_ID,CODIGO,ETIQUETA,DESCRIPCION,POSICION,ESTADO,FE_CREACION,USR_CREACION,FE_ULT_MOD,USR_ULT_MOD,IP_CREACION) values (DB_COMPROBANTES.SEQ_INFO_CAMPO_ADICIONAL.NEXTVAL,(SELECT ID_EMPRESA FROM DB_COMPROBANTES.ADMI_EMPRESA WHERE CODIGO = 'EN'),'1','telfCliente','Tel'||chr(38)||'eacute;fono',null,'6','Activo',sysdate,'atarreaga',null,null,'127.0.0.1');
Insert into DB_COMPROBANTES.INFO_CAMPO_ADICIONAL (ID_CAMPO_ADICIONAL,EMPRESA_ID,TIPO_DOC_ID,CODIGO,ETIQUETA,DESCRIPCION,POSICION,ESTADO,FE_CREACION,USR_CREACION,FE_ULT_MOD,USR_ULT_MOD,IP_CREACION) values (DB_COMPROBANTES.SEQ_INFO_CAMPO_ADICIONAL.NEXTVAL,(SELECT ID_EMPRESA FROM DB_COMPROBANTES.ADMI_EMPRESA WHERE CODIGO = 'EN'),'1','dirCliente','Direcci'||chr(38)||'oacute;n',null,'5','Activo',sysdate,'atarreaga',null,null,'127.0.0.1');
Insert into DB_COMPROBANTES.INFO_CAMPO_ADICIONAL (ID_CAMPO_ADICIONAL,EMPRESA_ID,TIPO_DOC_ID,CODIGO,ETIQUETA,DESCRIPCION,POSICION,ESTADO,FE_CREACION,USR_CREACION,FE_ULT_MOD,USR_ULT_MOD,IP_CREACION) values (DB_COMPROBANTES.SEQ_INFO_CAMPO_ADICIONAL.NEXTVAL,(SELECT ID_EMPRESA FROM DB_COMPROBANTES.ADMI_EMPRESA WHERE CODIGO = 'EN'),'1','ciudadCliente','Ciudad',null,'4','Activo',sysdate,'atarreaga',null,null,'127.0.0.1');
Insert into DB_COMPROBANTES.INFO_CAMPO_ADICIONAL (ID_CAMPO_ADICIONAL,EMPRESA_ID,TIPO_DOC_ID,CODIGO,ETIQUETA,DESCRIPCION,POSICION,ESTADO,FE_CREACION,USR_CREACION,FE_ULT_MOD,USR_ULT_MOD,IP_CREACION) values (DB_COMPROBANTES.SEQ_INFO_CAMPO_ADICIONAL.NEXTVAL,(SELECT ID_EMPRESA FROM DB_COMPROBANTES.ADMI_EMPRESA WHERE CODIGO = 'EN'),'1','fpagoCliente','Forma de Pago',null,'3','Inactivo',sysdate,'atarreaga',null,null,'127.0.0.1');
Insert into DB_COMPROBANTES.INFO_CAMPO_ADICIONAL (ID_CAMPO_ADICIONAL,EMPRESA_ID,TIPO_DOC_ID,CODIGO,ETIQUETA,DESCRIPCION,POSICION,ESTADO,FE_CREACION,USR_CREACION,FE_ULT_MOD,USR_ULT_MOD,IP_CREACION) values (DB_COMPROBANTES.SEQ_INFO_CAMPO_ADICIONAL.NEXTVAL,(SELECT ID_EMPRESA FROM DB_COMPROBANTES.ADMI_EMPRESA WHERE CODIGO = 'EN'),'1','consumoCliente','Consumo',null,'2','Activo',sysdate,'atarreaga',null,null,'127.0.0.1');
Insert into DB_COMPROBANTES.INFO_CAMPO_ADICIONAL (ID_CAMPO_ADICIONAL,EMPRESA_ID,TIPO_DOC_ID,CODIGO,ETIQUETA,DESCRIPCION,POSICION,ESTADO,FE_CREACION,USR_CREACION,FE_ULT_MOD,USR_ULT_MOD,IP_CREACION) values (DB_COMPROBANTES.SEQ_INFO_CAMPO_ADICIONAL.NEXTVAL,(SELECT ID_EMPRESA FROM DB_COMPROBANTES.ADMI_EMPRESA WHERE CODIGO = 'EN'),'1','loginCliente','Login',null,'1','Activo',sysdate,'atarreaga',null,null,'127.0.0.1');

COMMIT;

--INFO_CERTIFICADO
Insert into DB_COMPROBANTES.INFO_CERTIFICADO (ID_CERTIFICADO,EMPRESA_ID,NOMBRE,CLAVE,ESTADO,FE_CREACION,USR_CREACION,FE_ULT_MOD,USR_ULT_MOD,IP_CREACION,FORMATO_ID,RUTA,VERSION,TIPO) values (DB_COMPROBANTES.SEQ_INFO_CERTIFICADO.NEXTVAL,(SELECT ID_EMPRESA FROM DB_COMPROBANTES.ADMI_EMPRESA WHERE CODIGO = 'EN'),'MEGADATOS','Megadatos2021','ACTIVO',sysdate,'atarreaga',null,null,'127.0.0.1','3','/firma/system/certificate/1791287541001/3/','3','PKCS12');

--INFO_CAMPO_ADICIONAL
SET DEFINE OFF;
Insert into DB_COMPROBANTES.INFO_NOTIFICACION (ID_NOTIFICACION,EMPRESA_ID,MARCA,CORREO_NOTIFICACION,CORREO_CONTACTO,TELEFONO_CONTACTO,ASUNTO,CUERPO,VERSION_PLANTILLA,XML,HTML,PDF,PAGINA,DIRECCION,CORREO_FONTSIZE,USR_CREACION,FE_CREACION,USR_ULT_MOD,FE_ULT_MOD,IP_CREACION,MENSAJE_ADICIONAL) 
values (DB_COMPROBANTES.SEQ_INFO_NOTIFICACION.NEXTVAL,(SELECT ID_EMPRESA FROM DB_COMPROBANTES.ADMI_EMPRESA WHERE CODIGO = 'EN'),
'ECUANET','facturacionelectronica@ecuanet.com.ec','cobranzas@ecuanet.ec','39 20000','acaba de emitir un Comprobante Electronico de tu Servicio de Internet de Alta Velocidad.','<meta charset="UTF-8"/>
<div style="width:100%;">
<div style="font-family: Arial; font-size: 11px; text-align: justify; text-justify: inter-word">
<p>Estimado(a), <strong>$cliente_nombre</strong><br/>Gracias por ser parte de $empresa_marca.
A continuaci'||chr(38)||'oacute;n adjuntamos el Comprobante electr'||chr(38)||'oacute;nico en formato XML y su interpretaci'||chr(38)||'oacute;n en formato PDF
de tu $comprobante_tipo ELECTR'||chr(38)||'Oacute;NICO(A) que hemos generado en cumplimiento de la Resoluci'||chr(38)||'oacute;n No.NAC-DGERCGC12-00105
emitida por el SRI. Dentro de nuestro sistema tenemos registrado tu servicio con la siguiente Identificaci'||chr(38)||'oacute;n No.
<strong>$cliente_identificacion</strong>.
<br/>
</p><p>$mensajeAdicional</p>
<br/>
$MENSAJE_NOTIFICACION_FP <br/>
</div>
<div style="font-family: Arial; font-size: 11px; text-align: justify; text-justify: inter-word">
Para tu comodidad te facilitamos nuestros canales virtuales de pago, si registras forma de pago con debito bancario recuerda constatar que no registres el debito previo a que realices el pago directo. Haz aqu'||chr(38)||'iacute; click y pago tu saldo aqu'||chr(38)||'iacute; https://store.netlife.net.ec/autenticacion/login/
<p></p><p style="font-family: Arial; font-size: 11px; text-align: justify; text-justify: inter-word;">Favor no utilizar la
opci'||chr(38)||'oacute;n de responder a la direcci'||chr(38)||'oacute;n de correo electr'||chr(38)||'oacute;nico
de env'||chr(38)||'iacute;o, para consultas puede contactarnos al $empresa_telefono o comunicarse al mail $empresa_correo_contacto
</p>
A continuaci'||chr(38)||'oacute;n te presentamos el resumen de tu $comprobante_tipo actual:
<p></p>
</div>
<div style="width:700px; max-width:700px; padding: 20px 50px; background:url(''$FONDO_EXTRA'') no-repeat center; background-size: auto 100%;">$EXTRA</div>
<br/><br/>
<div style="text-align:center; font-size: 12px;">
<img alt="$empresa_marca" style="width:100%;" src="http://facturacion.telconet.net/img/$empresa_codigo/footer.png"/>
</div>
</div>

','1.0.0','1','1','1','http://netlife.ec','Matriz Quito: Nu'||chr(38)||'ntilde;ez de Vela E3-13 y Atahualpa. Edificio torre del Puente 2do Piso | Sucursal Guayaquil: Av. Rodrigo de Ch'||chr(38)||'aacute;vez Parque Empresarial Col'||chr(38)||'oacute;n Edif. ColonCorp Torre 6  Locales comerciales 4 y 5','10','atarreaga',sysdate,'atarreaga',sysdate,'127.0.0.1','Para consultas o requerimientos puede contactarse a nuestro Centro de Atenci'||chr(38)||'oacute;n a nivel nacional al $empresa_telefono. Si su reclamo NO ha sido resuelto por el prestador, ingrese su queja a trav'||chr(38)||'eacute;s del formulario respectivo en www.gob.ec');


--INFO_NOTIF_PLANTILLA

SET DEFINE OFF;
Insert into DB_COMPROBANTES.INFO_NOTIF_PLANTILLA (ID_NOTIF_PLANTILLA,PADRE_ID,TIPO_DOC_CODIGO,PLANTILLA,POSICION,VERSION,USR_CREACION,FE_CREACION,USR_ULT_MOD,FE_ULT_MOD,IP_CREACION) values (DB_COMPROBANTES.SEQ_INFO_NOTIF_PLANTILLA.NEXTVAL,(SELECT ID_NOTIFICACION FROM DB_COMPROBANTES.INFO_NOTIFICACION WHERE MARCA = 'ECUANET'),'00',
'<table id="ride_datos_empresa" style="width:98%; height:100%;">
<tr style="width:100%;">
<td style="text-align:left;">
<div style="width:100%; height:110px;">
<img style="width:300px;" src="http://facturacion.telconet.net/img/$empresa_codigo/logo.png" alt="$empresa_nombre"/>
</div>
</td>
</tr>
<tr style="width:100%;">
<td>
<table id="ride_datos_empresa_detalle" style="width:100%; height:100%; border: 1px solid black; border-radius:5px; margin:5px;">
<tr>
<td colspan="2" style="font-weight:bold; font-size:18px; padding:20px 15px;">$empresa_nombre</td>
</tr>
<tr>
<td style="font-weight:bold; font-size: $fontsizepx; padding:5px; vertical-align:top;">Dir Matriz:</td>
<td style="font-size: $fontsizepx; vertical-align:top;">$empresa_matriz</td>
</tr>
<tr>
<td style="font-weight:bold; font-size: $fontsizepx; padding:5px; vertical-align:top;">Dir Sucursal:</td>
<td style="font-size: $fontsizepx; vertical-align:top;">$empresa_sucursal</td>
</tr>
<tr style="display: $display_empresa_numero_contribuyente ">
<td style="font-weight:bold; font-size: $fontsizepx; padding:5px; vertical-align:top;">$label_empresa_contribuyente</td>
<td style="font-size: $fontsizepx; ">$empresa_numero_contribuyente</td>
</tr>
<tr>
<td style="font-weight:bold; font-size: $fontsizepx; padding:5px; vertical-align:top;">OBLIGADO A LLEVAR CONTABILIDAD:</td>
<td style="font-size: $fontsizepx;">$empresa_contabilidad</td>
</tr>
<tr style="display: $display_empresa_agente_retencion ">
<td style="font-weight:bold; font-size: $fontsizepx; padding:5px; vertical-align:top;">Agente de Retenci&oacute;n Resoluci&oacute;n No:</td>
<td style="font-size: $fontsizepx;">$empresa_agente_retencion</td>
</tr>
</table>
</td>
</tr>
</table>','1','3.0.0','atarreaga',sysdate,null,null,'127.0.0.1');

Insert into DB_COMPROBANTES.INFO_NOTIF_PLANTILLA (ID_NOTIF_PLANTILLA,PADRE_ID,TIPO_DOC_CODIGO,PLANTILLA,POSICION,VERSION,USR_CREACION,FE_CREACION,USR_ULT_MOD,FE_ULT_MOD,IP_CREACION) values (DB_COMPROBANTES.SEQ_INFO_NOTIF_PLANTILLA.NEXTVAL,(SELECT ID_NOTIFICACION FROM DB_COMPROBANTES.INFO_NOTIFICACION WHERE MARCA = 'ECUANET'),'00',
'<table id="ride_datos_comp" style="width:100%; height:100%; border: 1px solid black; border-radius:5px;">
<tr>
<td style="font-weight:bold; font-size: $fontsizepx; padding:5px;">R.U.C.:</td>
<td style="font-size: $fontsizepx;">$empresa_ruc</td>
</tr>
<tr>
<td colspan="2" style="font-weight:bold; font-size:16px; padding:5px;">$comprobante_tipo</td>
</tr>
<tr>
<td style="font-weight:bold; font-size: $fontsizepx; padding:5px;">No.</td>
<td style="font-size: $fontsizepx;">$comprobante_numero</td>
</tr>
<tr>
<td colspan="2" style="font-weight:bold; font-size: $fontsizepx; padding:5px;">N&Uacute;MERO DE AUTORIZACI&Oacute;N</td>
</tr>
<tr>
<td colspan="2" style="font-size: 12px; padding:5px; text-align:center;">$comprobante_numero_autorizacion</td>
</tr>
<tr>
<td style="font-weight:bold; font-size: $fontsizepx; padding:5px;">
<div style="$mostrarFechaHoraAutorizacion">FECHA Y HORA DE<br/>AUTORIZACI&Oacute;N</div>
$espacioFechaHoraAutorizacion
</td>
<td style="font-size: $fontsizepx;">$comprobante_fecha_autorizacion</td>
</tr>
<tr>
<td style="font-weight:bold; font-size: $fontsizepx; padding:5px;">AMBIENTE:</td>
<td style="font-size: $fontsizepx;">$comprobante_ambiente</td>
</tr>
<tr>
<td style="font-weight:bold; font-size: $fontsizepx; padding:5px;">EMISI&Oacute;N:</td>
<td style="font-size: $fontsizepx;">$comprobante_emision</td>
</tr>
<tr>
<td colspan="2" style="font-weight:bold; font-size: $fontsizepx; padding:5px;">CLAVE DE ACCESO</td>
</tr>
<tr>
<td colspan="2" style="text-align:center; font-size: $fontsizepx; padding:5px;">
<img src="http://facturacion.telconet.net/img/clave/$comprobante_clave_acceso.png" />
$comprobante_clave_acceso
</td>
</tr>
</table>','2','3.0.0','atarreaga',sysdate,null,null,'127.0.0.1');


SET DEFINE OFF;
Insert into DB_COMPROBANTES.INFO_NOTIF_PLANTILLA (ID_NOTIF_PLANTILLA,PADRE_ID,TIPO_DOC_CODIGO,PLANTILLA,USR_CREACION,POSICION,VERSION,FE_CREACION,USR_ULT_MOD,FE_ULT_MOD,IP_CREACION) values (DB_COMPROBANTES.SEQ_INFO_NOTIF_PLANTILLA.NEXTVAL,(SELECT ID_NOTIFICACION FROM DB_COMPROBANTES.INFO_NOTIFICACION WHERE MARCA = 'ECUANET'),'04','<table  width="600" bgcolor="#f7f7f7"> <tbody> <tr> <td class="esd-structure es-p20t es-p20r es-p20l" style="background-color: #f7f7f7;" bgcolor="#f7f7f7" align="left"> $iniComent[if mso]> <table width="600" cellpadding="0" cellspacing="0"> <tr> <td width="400" valign="top"> <![endif]$finComent</td> </tr> <tr id="ride_bloque3"> <td  width="700" style="background-color:#f7f7f7;" bgcolor="#f7f7f7"> <table  width="700" class="es-left" cellspacing="0" cellpadding="0" align="left"> <tbody> <tr> <td class="es-m-p0r es-m-p20b esd-container-frame" width="360" valign="top" align="center"> <table width="700" cellspacing="0" cellpadding="0"> <tbody> <tr> <td class="mailpoet_text mailpoet_padded_vertical mailpoet_padded_side" style="border-collapse: collapse; word-break: break-word; word-wrap: break-word;" valign="top"> <table style="border-collapse: collapse; border-spacing: 0; mso-table-lspace: 0; mso-table-rspace: 0;" width="700" cellpadding="0"> <tbody> <tr> <td class="mailpoet_text mailpoet_padded_vertical mailpoet_padded_side" style="border-collapse: collapse; word-break: break-word; word-wrap: break-word; text-align: center; " valign="top"> <img src="https://fm.telconet.net/fmlurlsvc/?fewReq=:B:JVQwOzk6MCx8NzgkOixjbjc6OzA6Oyx5Y21ka35/eG83bj09Ojg6bmtsPzk/OD1vPGk/PDM7aTw7azs5PTIzaTw+Ojg6Ozhpayx+Nzs8PTI+Oj8yMzkse2NuNzk4M0RkcjxaOjoyOj86Jzk4M0RkcjxYOjoyOj86LHhpen43LGk3PzgsYm5mNzo='||chr(38)||'url=http%3a%2f%2fwww.netlife.ec%2fwp-content%2fuploads%2f2023%2f03%2fFOOTER-ECUANET-FACTURA_2.png'||chr(38)||'fmlBlkTk"  width="700" alt="" style="height:auto;max-width:700;-ms-interpolation-mode:bicubic;border:0;display:block;outline:none;text-align:center"> </td> </tr> </tbody> </table> </td> </tr> <tr> <td class="mailpoet_paragraph" height="40"  bgcolor="#023b9e" style="background-color:#023b9e!important;border-collapse:collapse;mso-ansi-font-size:16px;color:#000000;font-family:Arial,'||chr(38)||'#39;Helvetica Neue'||chr(38)||'#39;,Helvetica,sans-serif;font-size:15px;line-height:24px;mso-line-height-alt:24px;word-break:break-word;word-wrap:break-word;text-align:center"> <span style="color: #ffffff;"><strong>Call center: 7201200 | www.ecuanet.ec</strong></span> </td> </tr> </tbody> </table> </td> </tr> </tbody> </table> $iniComent[if mso]> </td> <td width="20"></td> <![endif]$finComent $iniComent[if mso]> </tr> </table> <![endif]$finComent </td> </tr> </tbody> </table>','atarreaga','4','3.0.0',sysdate,null,null,'127.0.0.1');
Insert into DB_COMPROBANTES.INFO_NOTIF_PLANTILLA (ID_NOTIF_PLANTILLA,PADRE_ID,TIPO_DOC_CODIGO,PLANTILLA,USR_CREACION,POSICION,VERSION,FE_CREACION,USR_ULT_MOD,FE_ULT_MOD,IP_CREACION) values (DB_COMPROBANTES.SEQ_INFO_NOTIF_PLANTILLA.NEXTVAL,(SELECT ID_NOTIFICACION FROM DB_COMPROBANTES.INFO_NOTIFICACION WHERE MARCA = 'ECUANET'),'04','<table> <tr> <td class="esd-structure es-p20t es-p20r es-p20l" style="background-color: #f7f7f7;" bgcolor="#f7f7f7" align="left" > $iniComent[if mso]> <table width="600" cellpadding="0" cellspacing="0"> <tr> <td width="600" valign="top"> <![endif]$finComent <table class="es-left" cellspacing="0" cellpadding="0" align="left"> <tbody> <tr> <td class="es-m-p20b esd-container-frame" width="700" align="left"> <table width="700" cellspacing="0" cellpadding="0 " > <tbody> <tr> <td class="esd-block-text" align="center"> <p style="font-size: 13px;"><strong>Los pagos del servicio pueden ser realizados en nuestros canales autorizados:</strong> <br></p> </td> </tr> <tr><td align="center"><img src="https://fm.telconet.net/fmlurlsvc/?fewReq=:B:JVQwOzk6MCx8NzgkOixjbjc6OzA6Oyx5Y21ka35/eG83OjM4PDI7PzkyPT5sbmlrOjxoO2k/bmtvODpuOz0zOztpPjg9b25ubyx+Nzs8PTI+Oj8yMzkse2NuNzk4M0RkcjxaOjoyOj86Jzk4M0RkcjxYOjoyOj86LHhpen43LGk3PzgsYm5mNzo='||chr(38)||'url=http%3a%2f%2fwww.netlife.ec%2fwp-content%2fuploads%2f2023%2f03%2fECUANET-FACTURA-CANALES-DE-PAGO.png'||chr(38)||'fmlBlkTk" width="489" alt="" style="height:auto;max-width:700;-ms-interpolation-mode:bicubic;border:0;display:block;outline:none;text-align:center"></img> </td></tr> <tr> <td class="esd-block-text" align="center"><br> <p style="font-size: 13px;">El personal de Ecuanet no recibe ning'||chr(38)||'uacute;n tipo de pago en efectivo o en cuentas personales.<br></p> </td> </tr> </tbody> </table> </td> </tr> </tbody> </table> $iniComent[if mso]> </td> <td width="600" valign="top"> <![endif]$finComent <table class="es-center" cellspacing="0" cellpadding="0" align="center"> <tbody style="background-color: #f7f7f7;" bgcolor="#f7f7f7"> <tr> <td class="esd-container-frame" width="600" align="center"> <table width="700" cellspacing="0" cellpadding="0"> <tbody style="background-color: #f7f7f7;" bgcolor="#f7f7f7"> <tr> <td class="esd-block-image"  align="center"> <p></p> <p style="font-size: 24px;"><b>PARA REQUERIMIENTOS O CONSULTAS DE<br> FACTURACI'||chr(38)||'Oacute;N Y COBRANZA CONT'||chr(38)||'Aacute;CTANOS</p> </b> </td> </tr> <tr> <td class="esd-block-image"  align="center"> <a href="mailto:cobranzas@ecuanet.ec" style="color:#21759B;text-decoration:underline"><img src="https://fm.telconet.net/fmlurlsvc/?fewReq=:B:JVQwOzk6MCx8NzgkOixjbjc6OzA6Oyx5Y21ka35/eG83OTluOWhvbzs4PjlvOjo+Pmw7Pms9M24yO289OjI5Om9rPjs6bz08bix+Nzs8PTI+Oj8yMzkse2NuNzk4M0RkcjxaOjoyOj86Jzk4M0RkcjxYOjoyOj86LHhpen43LGk3PzgsYm5mNzo='||chr(38)||'url=http%3a%2f%2fwww.netlife.ec%2fwp-content%2fuploads%2f2023%2f03%2fECUANET-FACTURA-BOTON-EMAIL.png'||chr(38)||'fmlBlkTk" width="204" alt="" style="height:auto;max-width:700;-ms-interpolation-mode:bicubic;border:0;display:block;outline:none;text-align:center"></img></a> </td> </tr> </tbody> </table> </td> </tr> </tbody> </table> </td> </tr> $iniComent[if mso]></td></tr> </table> <![endif]$finComent </td> </tr></table>','atarreaga','3','3.0.0',sysdate,null,null,'127.0.0.1');
Insert into DB_COMPROBANTES.INFO_NOTIF_PLANTILLA (ID_NOTIF_PLANTILLA,PADRE_ID,TIPO_DOC_CODIGO,PLANTILLA,USR_CREACION,POSICION,VERSION,FE_CREACION,USR_ULT_MOD,FE_ULT_MOD,IP_CREACION) values (DB_COMPROBANTES.SEQ_INFO_NOTIF_PLANTILLA.NEXTVAL,(SELECT ID_NOTIFICACION FROM DB_COMPROBANTES.INFO_NOTIFICACION WHERE MARCA = 'ECUANET'),'04','<table><tr style="background-color: #ffdf47;" bgcolor="#ffdf47"><td class="esd-structure es-p5" style="background-color: #ffdf47;" bgcolor="#ffdf47" align="left"><table width="700" cellspacing="0" cellpadding="0"><tbody><tr><td class="esd-container-frame" width="590" valign="top" align="center"><table width="700" cellspacing="0" cellpadding="0"><tbody><tr><td height="40" class="esd-block-text" align="center"><p style="color: #000000;"><strong style="font-size:16px; ">PER'||chr(38)||'Iacute;ODO $comprobante_periodo</strong></p></td></tr></tbody></table></td></tr></tbody></table></td></tr><tr><td class="esd-structure es-p20t es-p20r es-p20l" style="background-color: #f7f7f7;" bgcolor="#f7f7f7" align="left"><table width="700" cellspacing="0" cellpadding="0"><tbody><tr><td class="esd-container-frame" width="560" valign="top" align="center"><table width="80%" cellspacing="0" cellpadding="0"><tbody><tr><td class="esd-block-text" align="center"><h3 style="text-align: center;"><strong style="font-size:16px;">$cliente_razon_social</strong></h3><p><strong style="font-size:16px;">Identificaci'||chr(38)||'oacute;n: $cliente_identificacion</strong><br></p></td></tr></tbody></table></td></tr></tbody></table></td></tr><tr><td class="esd-structure es-p15t es-p20l" style="background-color: #f7f7f7;border: 0px solid #666;" bgcolor="#f7f7f7" align="center"> $iniComent[if mso]><table width="600" cellpadding="0" cellspacing="0"><tr><td width="580" valign="top"> <![endif]$finComent<table class="es-center" cellspacing="0" cellpadding="0" align="center"><tbody><tr><td class="es-m-p20b esd-container-frame" width="600" align="left"><table width="90%" cellspacing="0" cellpadding="0" align="center" style="padding-left: 25px;padding-right: 25px;"><tbody><tr><td style="background-color: #ffffff;border: 2px solid #ccc; border-radius: 15px; padding-top: 5px;padding-bottom:5px;padding-left: 5px;padding-right:5px;font-size:16px;line-height:24px;" class="esd-block-text" align="left"><p style="line-height: 1.4; margin-bottom: 50px;"><strong> Documento:</strong> $comprobante_tipo <strong>'||chr(38)||'nbsp; Nro.:</strong>$comprobante_numero <br> <strong> N'||chr(38)||'uacute;mero de autorizaci'||chr(38)||'oacute;n:</strong>$comprobante_numero_autorizacion<br> <strong> Fecha de emisi'||chr(38)||'oacute;n:</strong> $comprobante_fecha_emision<br> <strong> Forma de pago: </strong>$forma_pago_cliente<strong ><br><p align="center"><strong style="font-size: 18px; ">TOTAL:</strong> <strong style="text-align: center;font-size: 18px; ">$$detalle_valor_total</strong></p></p></td></tr><tr><td class="esd-block-text" align="left"><br><p style="text-align: center;"><br></p></td></tr></tbody></table></td></tr></tbody></table> $iniComent[if mso]></td><td width="20"></td></tr></table> <![endif]$finComent</td></tr></table>','atarreaga','2','3.0.0',sysdate,null,null,'127.0.0.1');
Insert into DB_COMPROBANTES.INFO_NOTIF_PLANTILLA (ID_NOTIF_PLANTILLA,PADRE_ID,TIPO_DOC_CODIGO,PLANTILLA,USR_CREACION,POSICION,VERSION,FE_CREACION,USR_ULT_MOD,FE_ULT_MOD,IP_CREACION) values (DB_COMPROBANTES.SEQ_INFO_NOTIF_PLANTILLA.NEXTVAL,(SELECT ID_NOTIFICACION FROM DB_COMPROBANTES.INFO_NOTIFICACION WHERE MARCA = 'ECUANET'),'04','$iniComent[if gte mso 9]> <v:background xmlns:v="urn:schemas-microsoft-com:vml" fill="t"> <v:fill type="tile" color="#f8f9fd"></v:fill> </v:background> <![endif]$finComent<table class="es-wrapper" width="100%" cellspacing="0" cellpadding="0"><tbody><tr><td class="esd-email-paddings" valign="top"><table class="es-content esd-header-popover" cellspacing="0" cellpadding="0" align="center"><tbody><tr><td class="esd-stripe"  align="center"><table class="es-content-body" width="700" cellspacing="0" cellpadding="0"  align="center"><tbody><tr><td class="esd-structure es-p10t es-p15b es-p30r es-p30l"  align="left"><table width="100%" cellspacing="0" cellpadding="0"><tbody><tr><td class="esd-container-frame" width="540" valign="top" align="center"><table  width="100%" cellspacing="0" cellpadding="1" ><tbody><tr><td class="esd-block-image" style="font-size: 0px;" align="center"><a target="_blank"><img src="https://fm.telconet.net/fmlurlsvc/?fewReq=:B:JVQwOzk6MCx8NzgkOixjbjc6OzA6Oyx5Y21ka35/eG83Mj4+bmk8MjxvPD89OjpuOGs/OG5vOWtpbD07bDg+aT85PDpoODg6bCx+Nzs8PTI+Oj8yMzkse2NuNzk4M0RkcjxaOjoyOj86Jzk4M0RkcjxYOjoyOj86LHhpen43LGk3PzgsYm5mNzo='||chr(38)||'url=http%3a%2f%2fwww.netlife.ec%2fwp-content%2fuploads%2f2023%2f03%2fHEADER-ECUANET-FACTURA_.png'||chr(38)||'fmlBlkTk" width="100%" alt="" style="display: block;" class="adapt-img" width="540"></a></td></tr></tbody></table></td></tr></tbody></table></td></tr></tbody></table></td></tr></tbody></table></td></tr></tbody></table><![endif]$finComent','atarreaga','1','3.0.0',sysdate,null,null,'127.0.0.1');
Insert into DB_COMPROBANTES.INFO_NOTIF_PLANTILLA (ID_NOTIF_PLANTILLA,PADRE_ID,TIPO_DOC_CODIGO,PLANTILLA,USR_CREACION,POSICION,VERSION,FE_CREACION,USR_ULT_MOD,FE_ULT_MOD,IP_CREACION) values (DB_COMPROBANTES.SEQ_INFO_NOTIF_PLANTILLA.NEXTVAL,(SELECT ID_NOTIFICACION FROM DB_COMPROBANTES.INFO_NOTIFICACION WHERE MARCA = 'ECUANET'),'04','<table id="ride" align="center" style="width:600px; max-width:600px;  border-collapse: collapse; font-size:10px;">
    <tr id="ride_bloque1">
        <td style="width: 100%;">
          $BLOQUE_1
        </td>
    </tr>
    <tr id="ride_bloque2">
        <td>
          $BLOQUE_2  
        </td>
    </tr>
    <tr id="ride_bloque3">
        <td>
          $BLOQUE_3
        </td>
    </tr>
    <tr id="ride_bloque4">
        <td>
          $BLOQUE_4
        </td>
    </tr> 
</table>','atarreaga','0','3.0.0',sysdate,null,null,'127.0.0.1');
SET DEFINE OFF;
Insert into DB_COMPROBANTES.INFO_NOTIF_PLANTILLA (ID_NOTIF_PLANTILLA,PADRE_ID,TIPO_DOC_CODIGO,PLANTILLA,USR_CREACION,POSICION,VERSION,FE_CREACION,USR_ULT_MOD,FE_ULT_MOD,IP_CREACION) values (DB_COMPROBANTES.SEQ_INFO_NOTIF_PLANTILLA.NEXTVAL,(SELECT ID_NOTIFICACION FROM DB_COMPROBANTES.INFO_NOTIFICACION WHERE MARCA = 'ECUANET'),'01','<table  width="600" bgcolor="#f7f7f7"> <tbody> <tr> <td class="esd-structure es-p20t es-p20r es-p20l" style="background-color: #f7f7f7;" bgcolor="#f7f7f7" align="left"> $iniComent[if mso]> <table width="600" cellpadding="0" cellspacing="0"> <tr> <td width="400" valign="top"> <![endif]$finComent</td> </tr> <tr id="ride_bloque3"> <td  width="700" style="background-color:#f7f7f7;" bgcolor="#f7f7f7"> <table  width="700" class="es-left" cellspacing="0" cellpadding="0" align="left"> <tbody> <tr> <td class="es-m-p0r es-m-p20b esd-container-frame" width="360" valign="top" align="center"> <table width="700" cellspacing="0" cellpadding="0"> <tbody> <tr> <td class="mailpoet_text mailpoet_padded_vertical mailpoet_padded_side" style="border-collapse: collapse; word-break: break-word; word-wrap: break-word;" valign="top"> <table style="border-collapse: collapse; border-spacing: 0; mso-table-lspace: 0; mso-table-rspace: 0;" width="700" cellpadding="0"> <tbody> <tr> <td class="mailpoet_text mailpoet_padded_vertical mailpoet_padded_side" style="border-collapse: collapse; word-break: break-word; word-wrap: break-word; text-align: center; " valign="top"> <img src="https://fm.telconet.net/fmlurlsvc/?fewReq=:B:JVQwOzk6MCx8NzgkOixjbjc6OzA6Oyx5Y21ka35/eG83bj09Ojg6bmtsPzk/OD1vPGk/PDM7aTw7azs5PTIzaTw+Ojg6Ozhpayx+Nzs8PTI+Oj8yMzkse2NuNzk4M0RkcjxaOjoyOj86Jzk4M0RkcjxYOjoyOj86LHhpen43LGk3PzgsYm5mNzo='||chr(38)||'url=http%3a%2f%2fwww.netlife.ec%2fwp-content%2fuploads%2f2023%2f03%2fFOOTER-ECUANET-FACTURA_2.png'||chr(38)||'fmlBlkTk"  width="700" alt="" style="height:auto;max-width:700;-ms-interpolation-mode:bicubic;border:0;display:block;outline:none;text-align:center"> </td> </tr> </tbody> </table> </td> </tr> <tr> <td class="mailpoet_paragraph" height="40"  bgcolor="#023b9e" style="background-color:#023b9e!important;border-collapse:collapse;mso-ansi-font-size:16px;color:#000000;font-family:Arial,'||chr(38)||'#39;Helvetica Neue'||chr(38)||'#39;,Helvetica,sans-serif;font-size:15px;line-height:24px;mso-line-height-alt:24px;word-break:break-word;word-wrap:break-word;text-align:center"> <span style="color: #ffffff;"><strong>Call center: 7201200 | www.ecuanet.ec</strong></span> </td> </tr> </tbody> </table> </td> </tr> </tbody> </table> $iniComent[if mso]> </td> <td width="20"></td> <![endif]$finComent $iniComent[if mso]> </tr> </table> <![endif]$finComent </td> </tr> </tbody> </table>','atarreaga','4','3.0.0',sysdate,null,null,'127.0.0.1');
Insert into DB_COMPROBANTES.INFO_NOTIF_PLANTILLA (ID_NOTIF_PLANTILLA,PADRE_ID,TIPO_DOC_CODIGO,PLANTILLA,USR_CREACION,POSICION,VERSION,FE_CREACION,USR_ULT_MOD,FE_ULT_MOD,IP_CREACION) values (DB_COMPROBANTES.SEQ_INFO_NOTIF_PLANTILLA.NEXTVAL,(SELECT ID_NOTIFICACION FROM DB_COMPROBANTES.INFO_NOTIFICACION WHERE MARCA = 'ECUANET'),'01','<table> <tr> <td class="esd-structure es-p20t es-p20r es-p20l" style="background-color: #f7f7f7;" bgcolor="#f7f7f7" align="left" > $iniComent[if mso]> <table width="600" cellpadding="0" cellspacing="0"> <tr> <td width="600" valign="top"> <![endif]$finComent <table class="es-left" cellspacing="0" cellpadding="0" align="left"> <tbody> <tr> <td class="es-m-p20b esd-container-frame" width="700" align="left"> <table width="700" cellspacing="0" cellpadding="0 " > <tbody> <tr> <td class="esd-block-text" align="center"> <p style="font-size: 13px;"><strong>Los pagos del servicio pueden ser realizados en nuestros canales autorizados:</strong> <br></p> </td> </tr> <tr><td align="center"><img src="https://fm.telconet.net/fmlurlsvc/?fewReq=:B:JVQwOzk6MCx8NzgkOixjbjc6OzA6Oyx5Y21ka35/eG83OjM4PDI7PzkyPT5sbmlrOjxoO2k/bmtvODpuOz0zOztpPjg9b25ubyx+Nzs8PTI+Oj8yMzkse2NuNzk4M0RkcjxaOjoyOj86Jzk4M0RkcjxYOjoyOj86LHhpen43LGk3PzgsYm5mNzo='||chr(38)||'url=http%3a%2f%2fwww.netlife.ec%2fwp-content%2fuploads%2f2023%2f03%2fECUANET-FACTURA-CANALES-DE-PAGO.png'||chr(38)||'fmlBlkTk" width="489" alt="" style="height:auto;max-width:700;-ms-interpolation-mode:bicubic;border:0;display:block;outline:none;text-align:center"></img> </td></tr> <tr> <td class="esd-block-text" align="center"><br> <p style="font-size: 13px;">El personal de Ecuanet no recibe ning'||chr(38)||'uacute;n tipo de pago en efectivo o en cuentas personales.<br></p> </td> </tr> </tbody> </table> </td> </tr> </tbody> </table> $iniComent[if mso]> </td> <td width="600" valign="top"> <![endif]$finComent <table class="es-center" cellspacing="0" cellpadding="0" align="center"> <tbody style="background-color: #f7f7f7;" bgcolor="#f7f7f7"> <tr> <td class="esd-container-frame" width="600" align="center"> <table width="700" cellspacing="0" cellpadding="0"> <tbody style="background-color: #f7f7f7;" bgcolor="#f7f7f7"> <tr> <td class="esd-block-image"  align="center"> <p></p> <p style="font-size: 24px;"><b>PARA REQUERIMIENTOS O CONSULTAS DE<br> FACTURACI'||chr(38)||'Oacute;N Y COBRANZA CONT'||chr(38)||'Aacute;CTANOS</p> </b> </td> </tr> <tr> <td class="esd-block-image"  align="center"> <a href="mailto:cobranzas@ecuanet.ec" style="color:#21759B;text-decoration:underline"><img src="https://fm.telconet.net/fmlurlsvc/?fewReq=:B:JVQwOzk6MCx8NzgkOixjbjc6OzA6Oyx5Y21ka35/eG83OTluOWhvbzs4PjlvOjo+Pmw7Pms9M24yO289OjI5Om9rPjs6bz08bix+Nzs8PTI+Oj8yMzkse2NuNzk4M0RkcjxaOjoyOj86Jzk4M0RkcjxYOjoyOj86LHhpen43LGk3PzgsYm5mNzo='||chr(38)||'url=http%3a%2f%2fwww.netlife.ec%2fwp-content%2fuploads%2f2023%2f03%2fECUANET-FACTURA-BOTON-EMAIL.png'||chr(38)||'fmlBlkTk" width="204" alt="" style="height:auto;max-width:700;-ms-interpolation-mode:bicubic;border:0;display:block;outline:none;text-align:center"></img></a> </td> </tr> </tbody> </table> </td> </tr> </tbody> </table> </td> </tr> $iniComent[if mso]></td></tr> </table> <![endif]$finComent </td> </tr></table>','atarreaga','3','3.0.0',sysdate,null,null,'127.0.0.1');
Insert into DB_COMPROBANTES.INFO_NOTIF_PLANTILLA (ID_NOTIF_PLANTILLA,PADRE_ID,TIPO_DOC_CODIGO,PLANTILLA,USR_CREACION,POSICION,VERSION,FE_CREACION,USR_ULT_MOD,FE_ULT_MOD,IP_CREACION) values (DB_COMPROBANTES.SEQ_INFO_NOTIF_PLANTILLA.NEXTVAL,(SELECT ID_NOTIFICACION FROM DB_COMPROBANTES.INFO_NOTIFICACION WHERE MARCA = 'ECUANET'),'01','<table><tr style="background-color: #ffdf47;" bgcolor="#ffdf47"><td class="esd-structure es-p5" style="background-color: #ffdf47;" bgcolor="#ffdf47" align="left"><table width="700" cellspacing="0" cellpadding="0"><tbody><tr><td class="esd-container-frame" width="590" valign="top" align="center"><table width="700" cellspacing="0" cellpadding="0"><tbody><tr><td height="40" class="esd-block-text" align="center"><p style="color: #000000;"><strong style="font-size:16px; ">PER'||chr(38)||'Iacute;ODO $comprobante_periodo</strong></p></td></tr></tbody></table></td></tr></tbody></table></td></tr><tr><td class="esd-structure es-p20t es-p20r es-p20l" style="background-color: #f7f7f7;" bgcolor="#f7f7f7" align="left"><table width="700" cellspacing="0" cellpadding="0"><tbody><tr><td class="esd-container-frame" width="560" valign="top" align="center"><table width="80%" cellspacing="0" cellpadding="0"><tbody><tr><td class="esd-block-text" align="center"><h3 style="text-align: center;"><strong style="font-size:16px;">$cliente_razon_social</strong></h3><p><strong style="font-size:16px;">Identificaci'||chr(38)||'oacute;n: $cliente_identificacion</strong><br></p></td></tr></tbody></table></td></tr></tbody></table></td></tr><tr><td class="esd-structure es-p15t es-p20l" style="background-color: #f7f7f7;border: 0px solid #666;" bgcolor="#f7f7f7" align="center"> $iniComent[if mso]><table width="600" cellpadding="0" cellspacing="0"><tr><td width="580" valign="top"> <![endif]$finComent<table class="es-center" cellspacing="0" cellpadding="0" align="center"><tbody><tr><td class="es-m-p20b esd-container-frame" width="600" align="left"><table width="90%" cellspacing="0" cellpadding="0" align="center" style="padding-left: 25px;padding-right: 25px;"><tbody><tr><td style="background-color: #ffffff;border: 2px solid #ccc; border-radius: 15px; padding-top: 5px;padding-bottom:5px;padding-left: 5px;padding-right:5px;font-size:16px;line-height:24px;" class="esd-block-text" align="left"><p style="line-height: 1.4; margin-bottom: 50px;"><strong> Documento:</strong> $comprobante_tipo <strong>'||chr(38)||'nbsp; Nro.:</strong>$comprobante_numero <br> <strong> N'||chr(38)||'uacute;mero de autorizaci'||chr(38)||'oacute;n:</strong>$comprobante_numero_autorizacion<br> <strong> Fecha de emisi'||chr(38)||'oacute;n:</strong> $comprobante_fecha_emision<br> <strong> Forma de pago: </strong>$forma_pago_cliente <br><strong> Fecha m'||chr(38)||'aacute;xima de pago:</strong> $comprobante_fecha_pago<br><p align="center"><strong style="font-size: 18px; ">TOTAL:</strong> <strong style="text-align: center;font-size: 18px; ">$$detalle_valor_total</strong></p></p></td></tr><tr><td class="esd-block-text" align="left"><br><p style="text-align: center;"><br></p></td></tr></tbody></table></td></tr></tbody></table> $iniComent[if mso]></td><td width="20"></td></tr></table> <![endif]$finComent</td></tr></table>','atarreaga','2','3.0.0',sysdate,null,null,'127.0.0.1');
Insert into DB_COMPROBANTES.INFO_NOTIF_PLANTILLA (ID_NOTIF_PLANTILLA,PADRE_ID,TIPO_DOC_CODIGO,PLANTILLA,USR_CREACION,POSICION,VERSION,FE_CREACION,USR_ULT_MOD,FE_ULT_MOD,IP_CREACION) values (DB_COMPROBANTES.SEQ_INFO_NOTIF_PLANTILLA.NEXTVAL,(SELECT ID_NOTIFICACION FROM DB_COMPROBANTES.INFO_NOTIFICACION WHERE MARCA = 'ECUANET'),'01','$iniComent[if gte mso 9]> <v:background xmlns:v="urn:schemas-microsoft-com:vml" fill="t"> <v:fill type="tile" color="#f8f9fd"></v:fill> </v:background> <![endif]$finComent<table class="es-wrapper" width="100%" cellspacing="0" cellpadding="0"><tbody><tr><td class="esd-email-paddings" valign="top"><table class="es-content esd-header-popover" cellspacing="0" cellpadding="0" align="center"><tbody><tr><td class="esd-stripe"  align="center"><table class="es-content-body" width="700" cellspacing="0" cellpadding="0"  align="center"><tbody><tr><td class="esd-structure es-p10t es-p15b es-p30r es-p30l"  align="left"><table width="100%" cellspacing="0" cellpadding="0"><tbody><tr><td class="esd-container-frame" width="540" valign="top" align="center"><table  width="100%" cellspacing="0" cellpadding="1" ><tbody><tr><td class="esd-block-image" style="font-size: 0px;" align="center"><a target="_blank"><img src="https://fm.telconet.net/fmlurlsvc/?fewReq=:B:JVQwOzk6MCx8NzgkOixjbjc6OzA6Oyx5Y21ka35/eG83Mj4+bmk8MjxvPD89OjpuOGs/OG5vOWtpbD07bDg+aT85PDpoODg6bCx+Nzs8PTI+Oj8yMzkse2NuNzk4M0RkcjxaOjoyOj86Jzk4M0RkcjxYOjoyOj86LHhpen43LGk3PzgsYm5mNzo='||chr(38)||'url=http%3a%2f%2fwww.netlife.ec%2fwp-content%2fuploads%2f2023%2f03%2fHEADER-ECUANET-FACTURA_.png'||chr(38)||'fmlBlkTk" width="100%" alt="" style="display: block;" class="adapt-img" width="540"></a></td></tr></tbody></table></td></tr></tbody></table></td></tr></tbody></table></td></tr></tbody></table></td></tr></tbody></table><![endif]$finComent','atarreaga','1','3.0.0',sysdate,null,null,'127.0.0.1');
Insert into DB_COMPROBANTES.INFO_NOTIF_PLANTILLA (ID_NOTIF_PLANTILLA,PADRE_ID,TIPO_DOC_CODIGO,PLANTILLA,USR_CREACION,POSICION,VERSION,FE_CREACION,USR_ULT_MOD,FE_ULT_MOD,IP_CREACION) values (DB_COMPROBANTES.SEQ_INFO_NOTIF_PLANTILLA.NEXTVAL,(SELECT ID_NOTIFICACION FROM DB_COMPROBANTES.INFO_NOTIFICACION WHERE MARCA = 'ECUANET'),'01','<table id="ride" align="center" style="width:600px; max-width:600px;  border-collapse: collapse; font-size:10px;">
    <tr id="ride_bloque1">
        <td style="width: 100%;">
          $BLOQUE_1
        </td>
    </tr>
    <tr id="ride_bloque2">
        <td>
          $BLOQUE_2  
        </td>
    </tr>
    <tr id="ride_bloque3">
        <td>
          $BLOQUE_3
        </td>
    </tr>
    <tr id="ride_bloque4">
        <td>
          $BLOQUE_4
        </td>
    </tr>
</table>','atarreaga','0','3.0.0',sysdate,null,null,'127.0.0.1');
Insert into DB_COMPROBANTES.INFO_NOTIF_PLANTILLA (ID_NOTIF_PLANTILLA,PADRE_ID,TIPO_DOC_CODIGO,PLANTILLA,USR_CREACION,POSICION,VERSION,FE_CREACION,USR_ULT_MOD,FE_ULT_MOD,IP_CREACION) values (DB_COMPROBANTES.SEQ_INFO_NOTIF_PLANTILLA.NEXTVAL,(SELECT ID_NOTIFICACION FROM DB_COMPROBANTES.INFO_NOTIFICACION WHERE MARCA = 'ECUANET'),'04','<table id="ride_datos_adicionales" align="right" style="width:90%; height:50%; border: 1px solid black; margin:5px;">  
    <tr>  
        <td colspan="2" style="text-align:center; font-weight:bold; font-size: 14px;">Informaci'||chr(38)||'oacute;n Adicional</td>  
    </tr>  
    <tr>  
        <td style="font-weight:bold; padding-left:15px; font-size: $fontsizepx;">Login</td>  
        <td style="font-size: $fontsizepx;">$cliente_login</td>  
    </tr>  
    <tr>  
        <td style="font-weight:bold; padding-left:15px; font-size: $fontsizepx;">Consumo</td>  
        <td style="font-size: $fontsizepx;">$cliente_consumo</td>  
    </tr>  
    <tr>  
        <td style="font-weight:bold; padding-left:15px; font-size: $fontsizepx;">Forma de Pago</td>  
        <td style="font-size: $fontsizepx;">$cliente_formapago</td>  
    </tr>  
    <tr>  
        <td style="font-weight:bold; padding-left:15px; font-size: $fontsizepx;">Ciudad</td>  
        <td style="font-size: $fontsizepx;">$cliente_ciudad</td>  
    </tr>  
    <tr>  
        <td style="font-weight:bold; padding-left:15px; width:30%; font-size: $fontsizepx;">Direcci'||chr(38)||'oacute;n</td>  
        <td style="width: 70%; font-size: $fontsizepx;">$cliente_direccion</td>  
    </tr>  
    <tr>  
        <td style="font-weight:bold; padding-left:15px; font-size: $fontsizepx;">Tel'||chr(38)||'eacute;fono</td>  
        <td style="font-size: $fontsizepx;">$cliente_telefono</td> 
    </tr>  
    <tr>  
        <td style="font-weight:bold; padding-left:15px; font-size: $fontsizepx;">Email</td>  
        <td style="font-size: $fontsizepx;">$cliente_email</td>  
    </tr>  
</table><p style="clear: both;">$empresa_mensaje_adicional</p>','atarreaga','5','1.0.0',sysdate,null,null,'127.0.0.1');
Insert into DB_COMPROBANTES.INFO_NOTIF_PLANTILLA (ID_NOTIF_PLANTILLA,PADRE_ID,TIPO_DOC_CODIGO,PLANTILLA,USR_CREACION,POSICION,VERSION,FE_CREACION,USR_ULT_MOD,FE_ULT_MOD,IP_CREACION) values (DB_COMPROBANTES.SEQ_INFO_NOTIF_PLANTILLA.NEXTVAL,(SELECT ID_NOTIFICACION FROM DB_COMPROBANTES.INFO_NOTIFICACION WHERE MARCA = 'ECUANET'),'04','<table id="ride_detalle_comp_total" align="right" style="width:95%; height:100%; border-collapse: collapse; margin-right:-5px;">
    <tr>
        <td style="border: 1px solid black; font-weight:bold; width:75%; font-size: $fontsizepx;">SUBTOTAL $iva_porcentaje_label</td>
        <td style="border: 1px solid black; text-align:right; padding-right:10px; width:25%; font-size: $fontsizepx;">$detalle_subtotal_iva</td>
    </tr>
    <tr>
        <td style="border: 1px solid black; font-weight:bold; font-size: $fontsizepx;">SUBTOTAL 0%</td>
        <td style="border: 1px solid black; text-align:right; padding-right:10px; font-size: $fontsizepx;">$detalle_subtotal_0</td>
    </tr>
    <tr>
        <td style="border: 1px solid black; font-weight:bold; font-size: $fontsizepx;">SUBTOTAL no objeto de IVA</td>
        <td style="border: 1px solid black; text-align:right; padding-right:10px; font-size: $fontsizepx;">$detalle_subtotal_no_iva</td>
    </tr>
    <tr>
        <td style="border: 1px solid black; font-weight:bold; font-size: $fontsizepx;">SUBTOTAL Exento de IVA</td>
        <td style="border: 1px solid black; text-align:right; padding-right:10px; font-size: $fontsizepx;">$detalle_subtotal_ex_iva</td>
    </tr>
    <tr>
        <td style="border: 1px solid black; font-weight:bold; font-size: $fontsizepx;">SUBTOTAL SIN IMPUESTOS</td>
        <td style="border: 1px solid black; text-align:right; padding-right:10px; font-size: $fontsizepx;">$detalle_subtotal_sin_imp</td>
    </tr>
    <tr>
        <td style="border: 1px solid black; font-weight:bold; font-size: $fontsizepx;">DESCUENTO</td>
        <td style="border: 1px solid black; text-align:right; padding-right:10px; font-size: $fontsizepx;">$detalle_descuento</td>
    </tr>
    <tr>
        <td style="border: 1px solid black; font-weight:bold; font-size: $fontsizepx;">ICE</td>
        <td style="border: 1px solid black; text-align:right; padding-right:10px; font-size: $fontsizepx;">$detalle_ice</td>
    </tr>
    <tr>
        <td style="border: 1px solid black; font-weight:bold; font-size: $fontsizepx;">IVA $iva_porcentaje_label</td>
        <td style="border: 1px solid black; text-align:right; padding-right:10px; font-size: $fontsizepx;">$detalle_iva</td>
    </tr>
    <tr>
        <td style="border: 1px solid black; font-weight:bold; font-size: $fontsizepx;">IRBPNR</td>
        <td style="border: 1px solid black; text-align:right; padding-right:10px; font-size: $fontsizepx;">$detalle_irbpnr</td>
    </tr>
    <tr>
        <td style="border: 1px solid black; font-weight:bold; font-size: $fontsizepx;">VALOR TOTAL</td>
        <td style="border: 1px solid black; text-align:right; padding-right:10px; font-size: $fontsizepx;">$detalle_valor_total</td>
    </tr>
<tr style="display:$valor_compensacion_display;">
        <td style="border: 1px solid black; font-weight:bold; font-size: $fontsizepx;"> (-) Descuento Solidario 2%IVA</td>
        <td style="border: 1px solid black; text-align:right; padding-right:10px; font-size: $fontsizepx;">$detalle_compensacion</td>
    </tr>   
<tr style="display:$valor_compensacion_display;">
        <td style="border: 1px solid black; font-weight:bold; font-size: $fontsizepx;">VALOR A PAGAR</td>
        <td style="border: 1px solid black; text-align:right; padding-right:10px; font-size: $fontsizepx;">$detalle_valor_a_pagar</td>
    </tr> 
               
</table>','atarreaga','6','1.0.0',sysdate,'atarreaga',sysdate,'127.0.0.1');
Insert into DB_COMPROBANTES.INFO_NOTIF_PLANTILLA (ID_NOTIF_PLANTILLA,PADRE_ID,TIPO_DOC_CODIGO,PLANTILLA,USR_CREACION,POSICION,VERSION,FE_CREACION,USR_ULT_MOD,FE_ULT_MOD,IP_CREACION) values (DB_COMPROBANTES.SEQ_INFO_NOTIF_PLANTILLA.NEXTVAL,(SELECT ID_NOTIFICACION FROM DB_COMPROBANTES.INFO_NOTIFICACION WHERE MARCA = 'ECUANET'),'04','<table id="ride_detalle_comp" style="width:99.5%; height:100%; border-collapse: collapse; margin-left:5px;">
    <thead>
        <tr>
            <th style="border: 1px solid black; text-align:center; font-size: $fontsizepx;">Cod.</th>
            <th style="border: 1px solid black; text-align:center; font-size: $fontsizepx;">Cod.<br/>Auxiliar</th>
            <th style="border: 1px solid black; text-align:center; font-size: $fontsizepx;">Cant.</th>
            <th style="border: 1px solid black; text-align:center; font-size: $fontsizepx;">Descripci'||chr(38)||'oacute;n</th>
            <th style="border: 1px solid black; text-align:center; font-size: $fontsizepx;">Detalle<br/>Adicional</th>                        
            <th style="border: 1px solid black; text-align:center; font-size: $fontsizepx;">Precio<br/>Unitario</th>
            <th style="border: 1px solid black; text-align:center; font-size: $fontsizepx;">Descuento</th>
            <th style="border: 1px solid black; text-align:center; font-size: $fontsizepx;">Precio<br/>Total</th>
        </tr>
    </thead>
    <tbody>
        <!--<tr>
            <td style="border: 1px solid black; text-align:center; font-size: $fontsizepx;">$detalle_cod_principal</td>
            <td style="border: 1px solid black; text-align:center; font-size: $fontsizepx;">$detalle_cod_auxiliar</td>
            <td style="border: 1px solid black; text-align:center; font-size: $fontsizepx;">$detalle_cantidad</td>
            <td style="border: 1px solid black; font-size: $fontsizepx;">$detalle_descripcion</td>
            <td style="border: 1px solid black; font-size: $fontsizepx;">$detalle_adicional</td>                        
            <td style="border: 1px solid black; text-align:right; padding-right:10px; font-size: $fontsizepx;">$detalle_precio_unitario</td>
            <td style="border: 1px solid black; text-align:right; padding-right:10px; font-size: $fontsizepx;">$detalle_descuento</td>
            <td style="border: 1px solid black; text-align:right; padding-right:10px; font-size: $fontsizepx;">$detalle_precio_total</td>
        </tr>-->
    </tbody>
</table>','atarreaga','4','1.0.0',sysdate,null,null,'127.0.0.1');
Insert into DB_COMPROBANTES.INFO_NOTIF_PLANTILLA (ID_NOTIF_PLANTILLA,PADRE_ID,TIPO_DOC_CODIGO,PLANTILLA,USR_CREACION,POSICION,VERSION,FE_CREACION,USR_ULT_MOD,FE_ULT_MOD,IP_CREACION) values (DB_COMPROBANTES.SEQ_INFO_NOTIF_PLANTILLA.NEXTVAL,(SELECT ID_NOTIFICACION FROM DB_COMPROBANTES.INFO_NOTIFICACION WHERE MARCA = 'ECUANET'),'04','<table id="ride_datos_cliente" style="width:100%; height:100%; border: 1px solid black; margin:5px;">
    <tr>
        <td style="font-weight:bold; font-size: $fontsizepx; padding:5px;">Raz'||chr(38)||'oacute;n Social / Nombres y Apellidos:</td>
        <td style="font-size: $fontsizepx;">$cliente_razon_social</td>
        <td style="font-weight:bold; font-size: $fontsizepx; padding:5px;">Identificaci'||chr(38)||'oacute;n:</td>
        <td style="font-size: $fontsizepx;">$cliente_identificacion</td>
    </tr>
    <tr>
        <td style="font-weight:bold; font-size: $fontsizepx; padding:5px;">Fecha de Emisi'||chr(38)||'oacute;n:</td>
        <td style="font-size: $fontsizepx;">$comprobante_fecha_emision</td>
        <td style="font-weight:bold; font-size: $fontsizepx; padding:5px;">'||chr(38)||'nbsp;</td>
        <td style="font-size: $fontsizepx;">'||chr(38)||'nbsp;</td>
    </tr>
    <tr>
        <td colspan="4" style="border: 1px solid black; border-bottom:none; border-left:none; border-right:none;"></td>
    </tr>
    <tr>
        <td colspan="2" style="font-weight:bold; font-size: $fontsizepx; padding:5px;">Comprobante que se modifica:</td>
        <td style="font-size: $fontsizepx;">$comprobante_doc_modificado</td>
        <td style="font-size: $fontsizepx;">$comprobante_num_doc_modificado</td>
    </tr>
    <tr>
        <td colspan="2" style="font-weight:bold; font-size: $fontsizepx; padding:5px;">Fecha Emisi'||chr(38)||'oacute;n (Comprobante a modificar):</td>
        <td style="font-size: $fontsizepx;">$comprobante_fechaemision_doc_sustento</td>
        <td style="font-size: $fontsizepx;">'||chr(38)||'nbsp;</td>
    </tr>
    <tr>
        <td colspan="2" style="font-weight:bold; font-size: $fontsizepx; padding:5px;">Raz'||chr(38)||'oacute;n de Modificaci'||chr(38)||'oacute;n:</td>
        <td colspan="2" style="font-size: $fontsizepx;">$comprobante_motivo</td>
    </tr>

</table>','atarreaga','3','1.0.0',sysdate,null,null,'127.0.0.1');
Insert into DB_COMPROBANTES.INFO_NOTIF_PLANTILLA (ID_NOTIF_PLANTILLA,PADRE_ID,TIPO_DOC_CODIGO,PLANTILLA,USR_CREACION,POSICION,VERSION,FE_CREACION,USR_ULT_MOD,FE_ULT_MOD,IP_CREACION) values (DB_COMPROBANTES.SEQ_INFO_NOTIF_PLANTILLA.NEXTVAL,(SELECT ID_NOTIFICACION FROM DB_COMPROBANTES.INFO_NOTIFICACION WHERE MARCA = 'ECUANET'),'01','<table id="ride_detalle_comp_total" align="right" style="width:95%; height:100%; border-collapse: collapse; margin-right:-5px;">
    <tr>
        <td style="border: 1px solid black; font-weight:bold; width:75%; font-size: $fontsizepx;">SUBTOTAL $iva_porcentaje_label</td>
        <td style="border: 1px solid black; text-align:right; padding-right:10px; width:25%; font-size: $fontsizepx;">$detalle_subtotal_iva</td>
    </tr>
    <tr>
        <td style="border: 1px solid black; font-weight:bold; font-size: $fontsizepx;">SUBTOTAL 0%</td>
        <td style="border: 1px solid black; text-align:right; padding-right:10px; font-size: $fontsizepx;">$detalle_subtotal_0</td>
    </tr>
    <tr>
        <td style="border: 1px solid black; font-weight:bold; font-size: $fontsizepx;">SUBTOTAL no objeto de IVA</td>
        <td style="border: 1px solid black; text-align:right; padding-right:10px; font-size: $fontsizepx;">$detalle_subtotal_no_iva</td>
    </tr>
    <tr>
        <td style="border: 1px solid black; font-weight:bold; font-size: $fontsizepx;">SUBTOTAL Exento de IVA</td>
        <td style="border: 1px solid black; text-align:right; padding-right:10px; font-size: $fontsizepx;">$detalle_subtotal_ex_iva</td>
    </tr>
    <tr>
        <td style="border: 1px solid black; font-weight:bold; font-size: $fontsizepx;">SUBTOTAL SIN IMPUESTOS</td>
        <td style="border: 1px solid black; text-align:right; padding-right:10px; font-size: $fontsizepx;">$detalle_subtotal_sin_imp</td>
    </tr>
    <tr>
        <td style="border: 1px solid black; font-weight:bold; font-size: $fontsizepx;">DESCUENTO</td>
        <td style="border: 1px solid black; text-align:right; padding-right:10px; font-size: $fontsizepx;">$detalle_descuento</td>
    </tr>
    <tr>
        <td style="border: 1px solid black; font-weight:bold; font-size: $fontsizepx;">ICE</td>
        <td style="border: 1px solid black; text-align:right; padding-right:10px; font-size: $fontsizepx;">$detalle_ice</td>
    </tr>
    <tr>
        <td style="border: 1px solid black; font-weight:bold; font-size: $fontsizepx;">IVA $iva_porcentaje_label</td>
        <td style="border: 1px solid black; text-align:right; padding-right:10px; font-size: $fontsizepx;">$detalle_iva</td>
    </tr>
    <tr>
        <td style="border: 1px solid black; font-weight:bold; font-size: $fontsizepx;">IRBPNR</td>
        <td style="border: 1px solid black; text-align:right; padding-right:10px; font-size: $fontsizepx;">$detalle_irbpnr</td>
    </tr>
    <tr>
        <td style="border: 1px solid black; font-weight:bold; font-size: $fontsizepx;">PROPINA</td>
        <td style="border: 1px solid black; text-align:right; padding-right:10px; font-size: $fontsizepx;">$detalle_propina</td>
    </tr>
    <tr>
        <td style="border: 1px solid black; font-weight:bold; font-size: $fontsizepx;">VALOR TOTAL</td>
        <td style="border: 1px solid black; text-align:right; padding-right:10px; font-size: $fontsizepx;">$detalle_valor_total</td>
    </tr>
<tr style="display:$valor_compensacion_display;">
        <td style="border: 1px solid black; font-weight:bold; font-size: $fontsizepx;"> (-)  Descuento Solidario 2%IVA</td>
        <td style="border: 1px solid black; text-align:right; padding-right:10px; font-size: $fontsizepx;">$detalle_compensacion</td>
    </tr>   
<tr style="display:$valor_compensacion_display;">
        <td style="border: 1px solid black; font-weight:bold; font-size: $fontsizepx;">VALOR A PAGAR</td>
        <td style="border: 1px solid black; text-align:right; padding-right:10px; font-size: $fontsizepx;">$detalle_valor_a_pagar</td>
    </tr>   
               
</table>','atarreaga','6','1.0.0',sysdate,'atarreaga',sysdate,'127.0.0.1');
Insert into DB_COMPROBANTES.INFO_NOTIF_PLANTILLA (ID_NOTIF_PLANTILLA,PADRE_ID,TIPO_DOC_CODIGO,PLANTILLA,USR_CREACION,POSICION,VERSION,FE_CREACION,USR_ULT_MOD,FE_ULT_MOD,IP_CREACION) values (DB_COMPROBANTES.SEQ_INFO_NOTIF_PLANTILLA.NEXTVAL,(SELECT ID_NOTIFICACION FROM DB_COMPROBANTES.INFO_NOTIFICACION WHERE MARCA = 'ECUANET'),'01','<table id="ride_datos_adicionales" align="right" style="width:90%; height:50%; border: 1px solid black; margin:5px;"> 
    <tr> 
        <td colspan="2" style="text-align:center; font-weight:bold; font-size: 14px;">Informaci'||chr(38)||'oacute;n Adicional</td> 
    </tr> 
    <tr> 
        <td style="font-weight:bold; padding-left:15px; font-size: $fontsizepx;">Login</td> 
        <td style="font-size: $fontsizepx;">$cliente_login</td> 
    </tr> 
    <tr> 
        <td style="font-weight:bold; padding-left:15px; font-size: $fontsizepx;">Consumo</td> 
        <td style="font-size: $fontsizepx;">$cliente_consumo</td> 
    </tr> 
    <tr> 
        <td style="font-weight:bold; padding-left:15px; font-size: $fontsizepx;">Forma de Pago</td> 
        <td style="font-size: $fontsizepx;">$cliente_formapago</td> 
    </tr> 
    <tr> 
        <td style="font-weight:bold; padding-left:15px; font-size: $fontsizepx;">Ciudad</td> 
        <td style="font-size: $fontsizepx;">$cliente_ciudad</td> 
    </tr> 
    <tr> 
        <td style="font-weight:bold; padding-left:15px; width:30%; font-size: $fontsizepx;">Direcci'||chr(38)||'oacute;n</td> 
        <td style="width: 70%; font-size: $fontsizepx;">$cliente_direccion</td> 
    </tr> 
    <tr> 
        <td style="font-weight:bold; padding-left:15px; font-size: $fontsizepx;">Tel'||chr(38)||'eacute;fono</td> 
        <td style="font-size: $fontsizepx;">$cliente_telefono</td> 
    </tr> 
    <tr> 
        <td style="font-weight:bold; padding-left:15px; font-size: $fontsizepx;">Email</td> 
        <td style="font-size: $fontsizepx;">$cliente_email</td>
    </tr> 
</table>
<p style="clear: both;">$empresa_mensaje_adicional</p>','atarreaga','5','1.0.0',sysdate,null,null,'127.0.0.1');
Insert into DB_COMPROBANTES.INFO_NOTIF_PLANTILLA (ID_NOTIF_PLANTILLA,PADRE_ID,TIPO_DOC_CODIGO,PLANTILLA,USR_CREACION,POSICION,VERSION,FE_CREACION,USR_ULT_MOD,FE_ULT_MOD,IP_CREACION) values (DB_COMPROBANTES.SEQ_INFO_NOTIF_PLANTILLA.NEXTVAL,(SELECT ID_NOTIFICACION FROM DB_COMPROBANTES.INFO_NOTIFICACION WHERE MARCA = 'ECUANET'),'01','<table id="ride_detalle_comp" style="width:99.5%; height:100%; border-collapse: collapse; margin-left:5px;">
    <thead>
        <tr>
            <th style="border: 1px solid black; text-align:center; font-size: $fontsizepx;">Cod.<br/>Principal</th>
            <th style="border: 1px solid black; text-align:center; font-size: $fontsizepx;">Cod.<br/>Auxiliar</th>
            <th style="border: 1px solid black; text-align:center; font-size: $fontsizepx;">Cant.</th>
            <th style="border: 1px solid black; text-align:center; font-size: $fontsizepx;">Descripci'||chr(38)||'oacute;n</th>
            <th style="border: 1px solid black; text-align:center; font-size: $fontsizepx;">Detalle<br/>Adicional</th>                        
            <th style="border: 1px solid black; text-align:center; font-size: $fontsizepx;">Precio<br/>Unitario</th>
            <th style="border: 1px solid black; text-align:center; font-size: $fontsizepx;">Descuento</th>
            <th style="border: 1px solid black; text-align:center; font-size: $fontsizepx;">Precio<br/>Total</th>
        </tr> 
    </thead>
    <tbody>
        <!--<tr>
            <td style="border: 1px solid black; text-align:center; font-size: $fontsizepx;">$detalle_cod_principal</td>
            <td style="border: 1px solid black; text-align:center; font-size: $fontsizepx;">$detalle_cod_auxiliar</td>
            <td style="border: 1px solid black; text-align:center; font-size: $fontsizepx;">$detalle_cantidad</td>
            <td style="border: 1px solid black; font-size: $fontsizepx;">$detalle_descripcion</td>
            <td style="border: 1px solid black; font-size: $fontsizepx;">$detalle_adicional</td>                        
            <td style="border: 1px solid black; text-align:right; padding-right:10px; font-size: $fontsizepx;">$detalle_precio_unitario</td>
            <td style="border: 1px solid black; text-align:right; padding-right:10px; font-size: $fontsizepx;">$detalle_descuento</td>
            <td style="border: 1px solid black; text-align:right; padding-right:10px; font-size: $fontsizepx;">$detalle_precio_total</td>
        </tr>-->
    </tbody>
</table>','atarreaga','4','1.0.0',sysdate,null,null,'127.0.0.1');
Insert into DB_COMPROBANTES.INFO_NOTIF_PLANTILLA (ID_NOTIF_PLANTILLA,PADRE_ID,TIPO_DOC_CODIGO,PLANTILLA,USR_CREACION,POSICION,VERSION,FE_CREACION,USR_ULT_MOD,FE_ULT_MOD,IP_CREACION) values (DB_COMPROBANTES.SEQ_INFO_NOTIF_PLANTILLA.NEXTVAL,(SELECT ID_NOTIFICACION FROM DB_COMPROBANTES.INFO_NOTIFICACION WHERE MARCA = 'ECUANET'),'01','<table id="ride_datos_cliente" style="width:100%; height:100%; border: 1px solid black; margin:5px;">
    <tr>
        <td style="font-weight:bold; font-size: $fontsizepx; padding:5px;">Raz'||chr(38)||'oacute;n Social / Nombres y Apellidos:</td>
        <td style="font-size: $fontsizepx;">$cliente_razon_social</td>
        <td style="font-weight:bold; font-size: $fontsizepx; padding:5px;">Identificaci'||chr(38)||'oacute;n:</td>
        <td style="font-size: $fontsizepx;">$cliente_identificacion</td>
    </tr>
    <tr>
        <td style="font-weight:bold; font-size: $fontsizepx; padding:5px;">Fecha de Emisi'||chr(38)||'oacute;n:</td>
        <td style="font-size: $fontsizepx;">$comprobante_fecha_emision</td>
        <td style="font-weight:bold; font-size: $fontsizepx; padding:5px;">Gu'||chr(38)||'iacute;a de Remisi'||chr(38)||'oacute;n:</td>
        <td style="font-size: $fontsizepx;">$comprobante_guia_remision</td>
    </tr>
</table>','atarreaga','3','1.0.0',sysdate,null,null,'127.0.0.1');



--ADMI_GESTION_DIRECTORIOS 
Insert into DB_GENERAL.ADMI_GESTION_DIRECTORIOS (ID_GESTION_DIRECTORIO,CODIGO_APP,CODIGO_PATH,APLICACION,PAIS,EMPRESA,MODULO,SUBMODULO,ESTADO,FE_CREACION,FE_ULT_MOD,USR_CREACION,USR_ULT_MOD) 
values (DB_GENERAL.SEQ_ADMI_GESTION_DIRECTORIOS.NEXTVAL,5,(SELECT MAX(CODIGO_PATH)+1 FROM DB_GENERAL.ADMI_GESTION_DIRECTORIOS WHERE CODIGO_APP = 5),'ComprobantesElectronicos','593','EN','EnvioDocumentos','Ride','Activo',sysdate,null,'atarreaga',null);

Insert into DB_GENERAL.ADMI_GESTION_DIRECTORIOS (ID_GESTION_DIRECTORIO,CODIGO_APP,CODIGO_PATH,APLICACION,PAIS,EMPRESA,MODULO,SUBMODULO,ESTADO,FE_CREACION,FE_ULT_MOD,USR_CREACION,USR_ULT_MOD) 
values (DB_GENERAL.SEQ_ADMI_GESTION_DIRECTORIOS.NEXTVAL,5,(SELECT MAX(CODIGO_PATH)+1 FROM DB_GENERAL.ADMI_GESTION_DIRECTORIOS WHERE CODIGO_APP = 5),'ComprobantesElectronicos','593','EN','EnvioDocumentos','XML','Activo',sysdate,null,'atarreaga',null);


---DB_GENERAL---
--CARGO REACTIVACION SERVICIO
Insert into DB_GENERAL.ADMI_PARAMETRO_DET (ID_PARAMETRO_DET,PARAMETRO_ID,DESCRIPCION,VALOR1,VALOR2,VALOR3,VALOR4,ESTADO,USR_CREACION,FE_CREACION,IP_CREACION,USR_ULT_MOD,FE_ULT_MOD,IP_ULT_MOD,VALOR5,EMPRESA_COD,VALOR6,VALOR7,OBSERVACION,VALOR8,VALOR9) 
values (DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,(SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'CARGO REACTIVACION SERVICIO'),'PARAMETROS CONFIGURABLES PARA CARGO POR REACTIVACION DE SERVICIO','3.00','01/01/2016',null,null,'Activo','atarreaga',sysdate,'127.0.0.1',null,null,null,null,'33',null,null,null,null,null);

--MOTIVOS_ELIMINAR_ORDEN_SERVICIO_VENDEDOR
Insert into DB_GENERAL.ADMI_PARAMETRO_DET (ID_PARAMETRO_DET,PARAMETRO_ID,DESCRIPCION,VALOR1,VALOR2,VALOR3,VALOR4,ESTADO,USR_CREACION,FE_CREACION,IP_CREACION,USR_ULT_MOD,FE_ULT_MOD,IP_ULT_MOD,VALOR5,EMPRESA_COD,VALOR6,VALOR7,OBSERVACION,VALOR8,VALOR9) 
values (DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,(SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'MOTIVOS_ELIMINAR_ORDEN_SERVICIO_VENDEDOR'),'MOTIVOS DE ELIMINACION DE LA ORDEN DE SERVICIO QUE GENERAN NOTA DE CREDITO POR VENDEDOR','S','Cliente ya no desea el servicio','2365',null,'Activo','atarreaga',sysdate,'127.0.0.1',null,null,null,null,'33',null,null,null,null,null);
Insert into DB_GENERAL.ADMI_PARAMETRO_DET (ID_PARAMETRO_DET,PARAMETRO_ID,DESCRIPCION,VALOR1,VALOR2,VALOR3,VALOR4,ESTADO,USR_CREACION,FE_CREACION,IP_CREACION,USR_ULT_MOD,FE_ULT_MOD,IP_ULT_MOD,VALOR5,EMPRESA_COD,VALOR6,VALOR7,OBSERVACION,VALOR8,VALOR9) 
values (DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,(SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'MOTIVOS_ELIMINAR_ORDEN_SERVICIO_VENDEDOR'),'MOTIVOS DE ELIMINACION DE LA ORDEN DE SERVICIO QUE GENERAN NOTA DE CREDITO POR VENDEDOR','S','Cambio de Forma de pago','1802',null,'Activo','atarreaga',sysdate,'127.0.0.1',null,null,null,null,'33',null,null,null,null,null);
Insert into DB_GENERAL.ADMI_PARAMETRO_DET (ID_PARAMETRO_DET,PARAMETRO_ID,DESCRIPCION,VALOR1,VALOR2,VALOR3,VALOR4,ESTADO,USR_CREACION,FE_CREACION,IP_CREACION,USR_ULT_MOD,FE_ULT_MOD,IP_ULT_MOD,VALOR5,EMPRESA_COD,VALOR6,VALOR7,OBSERVACION,VALOR8,VALOR9) 
values (DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,(SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'MOTIVOS_ELIMINAR_ORDEN_SERVICIO_VENDEDOR'),'MOTIVOS DE ELIMINACION DE LA ORDEN DE SERVICIO QUE GENERAN NOTA DE CREDITO POR VENDEDOR','S','Datos/plan incorrecto','1801',null,'Activo','atarreaga',sysdate,'127.0.0.1',null,null,null,null,'33',null,null,null,null,null);


--NC_MOTIVOS_ORDEN_SERVICIO
Insert into DB_GENERAL.ADMI_PARAMETRO_DET (ID_PARAMETRO_DET,PARAMETRO_ID,DESCRIPCION,VALOR1,VALOR2,VALOR3,VALOR4,ESTADO,USR_CREACION,FE_CREACION,IP_CREACION,USR_ULT_MOD,FE_ULT_MOD,IP_ULT_MOD,VALOR5,EMPRESA_COD,VALOR6,VALOR7,OBSERVACION,VALOR8,VALOR9) values (DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,(SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'NC_MOTIVOS_ORDEN_SERVICIO'),'PARAMETRO QUE VALIDA CREACION DE NOTA DE CREDITO SEGUN MOTIVOS','N','Cliente sin servicio',null,null,'Activo','atarreaga',sysdate,'127.0.0.1',null,null,null,null,33,null,null,null,null,null);
Insert into DB_GENERAL.ADMI_PARAMETRO_DET (ID_PARAMETRO_DET,PARAMETRO_ID,DESCRIPCION,VALOR1,VALOR2,VALOR3,VALOR4,ESTADO,USR_CREACION,FE_CREACION,IP_CREACION,USR_ULT_MOD,FE_ULT_MOD,IP_ULT_MOD,VALOR5,EMPRESA_COD,VALOR6,VALOR7,OBSERVACION,VALOR8,VALOR9) values (DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,(SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'NC_MOTIVOS_ORDEN_SERVICIO'),'PARAMETRO QUE VALIDA CREACION DE NOTA DE CREDITO SEGUN MOTIVOS','N','Cliente Cyber',null,null,'Activo','atarreaga',sysdate,'127.0.0.1',null,null,null,null,33,null,null,null,null,null);
Insert into DB_GENERAL.ADMI_PARAMETRO_DET (ID_PARAMETRO_DET,PARAMETRO_ID,DESCRIPCION,VALOR1,VALOR2,VALOR3,VALOR4,ESTADO,USR_CREACION,FE_CREACION,IP_CREACION,USR_ULT_MOD,FE_ULT_MOD,IP_ULT_MOD,VALOR5,EMPRESA_COD,VALOR6,VALOR7,OBSERVACION,VALOR8,VALOR9) values (DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,(SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'NC_MOTIVOS_ORDEN_SERVICIO'),'PARAMETRO QUE VALIDA CREACION DE NOTA DE CREDITO SEGUN MOTIVOS','N','Zona Regenerada',null,null,'Activo','atarreaga',sysdate,'127.0.0.1',null,null,null,null,33,null,null,null,null,null);
Insert into DB_GENERAL.ADMI_PARAMETRO_DET (ID_PARAMETRO_DET,PARAMETRO_ID,DESCRIPCION,VALOR1,VALOR2,VALOR3,VALOR4,ESTADO,USR_CREACION,FE_CREACION,IP_CREACION,USR_ULT_MOD,FE_ULT_MOD,IP_ULT_MOD,VALOR5,EMPRESA_COD,VALOR6,VALOR7,OBSERVACION,VALOR8,VALOR9) values (DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,(SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'NC_MOTIVOS_ORDEN_SERVICIO'),'PARAMETRO QUE VALIDA CREACION DE NOTA DE CREDITO SEGUN MOTIVOS','N','Fuera de Cobertura',null,null,'Activo','atarreaga',sysdate,'127.0.0.1',null,null,null,null,33,null,null,null,null,null);
Insert into DB_GENERAL.ADMI_PARAMETRO_DET (ID_PARAMETRO_DET,PARAMETRO_ID,DESCRIPCION,VALOR1,VALOR2,VALOR3,VALOR4,ESTADO,USR_CREACION,FE_CREACION,IP_CREACION,USR_ULT_MOD,FE_ULT_MOD,IP_ULT_MOD,VALOR5,EMPRESA_COD,VALOR6,VALOR7,OBSERVACION,VALOR8,VALOR9) values (DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,(SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'NC_MOTIVOS_ORDEN_SERVICIO'),'PARAMETRO QUE VALIDA CREACION DE NOTA DE CREDITO SEGUN MOTIVOS','N','Dirección Incorrecta',null,null,'Activo','atarreaga',sysdate,'127.0.0.1',null,null,null,null,33,null,null,null,null,null);
Insert into DB_GENERAL.ADMI_PARAMETRO_DET (ID_PARAMETRO_DET,PARAMETRO_ID,DESCRIPCION,VALOR1,VALOR2,VALOR3,VALOR4,ESTADO,USR_CREACION,FE_CREACION,IP_CREACION,USR_ULT_MOD,FE_ULT_MOD,IP_ULT_MOD,VALOR5,EMPRESA_COD,VALOR6,VALOR7,OBSERVACION,VALOR8,VALOR9) values (DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,(SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'NC_MOTIVOS_ORDEN_SERVICIO'),'PARAMETRO QUE VALIDA CREACION DE NOTA DE CREDITO SEGUN MOTIVOS','N','Demora en la instalación',null,null,'Activo','atarreaga',sysdate,'127.0.0.1',null,null,null,null,33,null,null,null,null,null);
Insert into DB_GENERAL.ADMI_PARAMETRO_DET (ID_PARAMETRO_DET,PARAMETRO_ID,DESCRIPCION,VALOR1,VALOR2,VALOR3,VALOR4,ESTADO,USR_CREACION,FE_CREACION,IP_CREACION,USR_ULT_MOD,FE_ULT_MOD,IP_ULT_MOD,VALOR5,EMPRESA_COD,VALOR6,VALOR7,OBSERVACION,VALOR8,VALOR9) values (DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,(SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'NC_MOTIVOS_ORDEN_SERVICIO'),'PARAMETRO QUE VALIDA CREACION DE NOTA DE CREDITO SEGUN MOTIVOS','N','Venta incorrecta',null,null,'Activo','atarreaga',sysdate,'127.0.0.1',null,null,null,null,33,null,null,null,null,null);
Insert into DB_GENERAL.ADMI_PARAMETRO_DET (ID_PARAMETRO_DET,PARAMETRO_ID,DESCRIPCION,VALOR1,VALOR2,VALOR3,VALOR4,ESTADO,USR_CREACION,FE_CREACION,IP_CREACION,USR_ULT_MOD,FE_ULT_MOD,IP_ULT_MOD,VALOR5,EMPRESA_COD,VALOR6,VALOR7,OBSERVACION,VALOR8,VALOR9) values (DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,(SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'NC_MOTIVOS_ORDEN_SERVICIO'),'PARAMETRO QUE VALIDA CREACION DE NOTA DE CREDITO SEGUN MOTIVOS','N','Zona Saturada',null,null,'Activo','atarreaga',sysdate,'127.0.0.1',null,null,null,null,33,null,null,null,null,null);
Insert into DB_GENERAL.ADMI_PARAMETRO_DET (ID_PARAMETRO_DET,PARAMETRO_ID,DESCRIPCION,VALOR1,VALOR2,VALOR3,VALOR4,ESTADO,USR_CREACION,FE_CREACION,IP_CREACION,USR_ULT_MOD,FE_ULT_MOD,IP_ULT_MOD,VALOR5,EMPRESA_COD,VALOR6,VALOR7,OBSERVACION,VALOR8,VALOR9) values (DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,(SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'NC_MOTIVOS_ORDEN_SERVICIO'),'PARAMETRO QUE VALIDA CREACION DE NOTA DE CREDITO SEGUN MOTIVOS','N','Contrato Duplicado',null,null,'Activo','atarreaga',sysdate,'127.0.0.1',null,null,null,null,33,null,null,null,null,null);
Insert into DB_GENERAL.ADMI_PARAMETRO_DET (ID_PARAMETRO_DET,PARAMETRO_ID,DESCRIPCION,VALOR1,VALOR2,VALOR3,VALOR4,ESTADO,USR_CREACION,FE_CREACION,IP_CREACION,USR_ULT_MOD,FE_ULT_MOD,IP_ULT_MOD,VALOR5,EMPRESA_COD,VALOR6,VALOR7,OBSERVACION,VALOR8,VALOR9) values (DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,(SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'NC_MOTIVOS_ORDEN_SERVICIO'),'PARAMETRO QUE VALIDA CREACION DE NOTA DE CREDITO SEGUN MOTIVOS','N','Coordenadas Incorrectas',null,null,'Activo','atarreaga',sysdate,'127.0.0.1',null,null,null,null,33,null,null,null,null,null);
Insert into DB_GENERAL.ADMI_PARAMETRO_DET (ID_PARAMETRO_DET,PARAMETRO_ID,DESCRIPCION,VALOR1,VALOR2,VALOR3,VALOR4,ESTADO,USR_CREACION,FE_CREACION,IP_CREACION,USR_ULT_MOD,FE_ULT_MOD,IP_ULT_MOD,VALOR5,EMPRESA_COD,VALOR6,VALOR7,OBSERVACION,VALOR8,VALOR9) values (DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,(SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'NC_MOTIVOS_ORDEN_SERVICIO'),'PARAMETRO QUE VALIDA CREACION DE NOTA DE CREDITO SEGUN MOTIVOS','N','Sector o Parroquia Incorrecto',null,null,'Activo','atarreaga',sysdate,'127.0.0.1',null,null,null,null,33,null,null,null,null,null);
Insert into DB_GENERAL.ADMI_PARAMETRO_DET (ID_PARAMETRO_DET,PARAMETRO_ID,DESCRIPCION,VALOR1,VALOR2,VALOR3,VALOR4,ESTADO,USR_CREACION,FE_CREACION,IP_CREACION,USR_ULT_MOD,FE_ULT_MOD,IP_ULT_MOD,VALOR5,EMPRESA_COD,VALOR6,VALOR7,OBSERVACION,VALOR8,VALOR9) values (DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,(SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'NC_MOTIVOS_ORDEN_SERVICIO'),'PARAMETRO QUE VALIDA CREACION DE NOTA DE CREDITO SEGUN MOTIVOS','N','Contrato digital mal ingresado',null,null,'Activo','atarreaga',sysdate,'127.0.0.1',null,null,null,null,33,null,null,null,null,null);
Insert into DB_GENERAL.ADMI_PARAMETRO_DET (ID_PARAMETRO_DET,PARAMETRO_ID,DESCRIPCION,VALOR1,VALOR2,VALOR3,VALOR4,ESTADO,USR_CREACION,FE_CREACION,IP_CREACION,USR_ULT_MOD,FE_ULT_MOD,IP_ULT_MOD,VALOR5,EMPRESA_COD,VALOR6,VALOR7,OBSERVACION,VALOR8,VALOR9) values (DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,(SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'NC_MOTIVOS_ORDEN_SERVICIO'),'PARAMETRO QUE VALIDA CREACION DE NOTA DE CREDITO SEGUN MOTIVOS','N','Cliente en urbanización instalar como zona abierta',null,null,'Activo','atarreaga',sysdate,'127.0.0.1',null,null,null,null,33,null,null,null,null,null);
Insert into DB_GENERAL.ADMI_PARAMETRO_DET (ID_PARAMETRO_DET,PARAMETRO_ID,DESCRIPCION,VALOR1,VALOR2,VALOR3,VALOR4,ESTADO,USR_CREACION,FE_CREACION,IP_CREACION,USR_ULT_MOD,FE_ULT_MOD,IP_ULT_MOD,VALOR5,EMPRESA_COD,VALOR6,VALOR7,OBSERVACION,VALOR8,VALOR9) values (DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,(SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'NC_MOTIVOS_ORDEN_SERVICIO'),'PARAMETRO QUE VALIDA CREACION DE NOTA DE CREDITO SEGUN MOTIVOS','N','Obra Civil por cliente no define fecha',null,null,'Activo','atarreaga',sysdate,'127.0.0.1',null,null,null,null,33,null,null,null,null,null);
Insert into DB_GENERAL.ADMI_PARAMETRO_DET (ID_PARAMETRO_DET,PARAMETRO_ID,DESCRIPCION,VALOR1,VALOR2,VALOR3,VALOR4,ESTADO,USR_CREACION,FE_CREACION,IP_CREACION,USR_ULT_MOD,FE_ULT_MOD,IP_ULT_MOD,VALOR5,EMPRESA_COD,VALOR6,VALOR7,OBSERVACION,VALOR8,VALOR9) values (DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,(SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'NC_MOTIVOS_ORDEN_SERVICIO'),'PARAMETRO QUE VALIDA CREACION DE NOTA DE CREDITO SEGUN MOTIVOS','N','No desea realizar obra civil',null,null,'Activo','atarreaga',sysdate,'127.0.0.1',null,null,null,null,33,null,null,null,null,null);
Insert into DB_GENERAL.ADMI_PARAMETRO_DET (ID_PARAMETRO_DET,PARAMETRO_ID,DESCRIPCION,VALOR1,VALOR2,VALOR3,VALOR4,ESTADO,USR_CREACION,FE_CREACION,IP_CREACION,USR_ULT_MOD,FE_ULT_MOD,IP_ULT_MOD,VALOR5,EMPRESA_COD,VALOR6,VALOR7,OBSERVACION,VALOR8,VALOR9) values (DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,(SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'NC_MOTIVOS_ORDEN_SERVICIO'),'PARAMETRO QUE VALIDA CREACION DE NOTA DE CREDITO SEGUN MOTIVOS','N','Cliente no desea pagar el excedente de Fibra',null,null,'Activo','atarreaga',sysdate,'127.0.0.1',null,null,null,null,33,null,null,null,null,null);
Insert into DB_GENERAL.ADMI_PARAMETRO_DET (ID_PARAMETRO_DET,PARAMETRO_ID,DESCRIPCION,VALOR1,VALOR2,VALOR3,VALOR4,ESTADO,USR_CREACION,FE_CREACION,IP_CREACION,USR_ULT_MOD,FE_ULT_MOD,IP_ULT_MOD,VALOR5,EMPRESA_COD,VALOR6,VALOR7,OBSERVACION,VALOR8,VALOR9) values (DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,(SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'NC_MOTIVOS_ORDEN_SERVICIO'),'PARAMETRO QUE VALIDA CREACION DE NOTA DE CREDITO SEGUN MOTIVOS','N','Ductos saturados cliente no desea pasar guías',null,null,'Activo','atarreaga',sysdate,'127.0.0.1',null,null,null,null,33,null,null,null,null,null);
Insert into DB_GENERAL.ADMI_PARAMETRO_DET (ID_PARAMETRO_DET,PARAMETRO_ID,DESCRIPCION,VALOR1,VALOR2,VALOR3,VALOR4,ESTADO,USR_CREACION,FE_CREACION,IP_CREACION,USR_ULT_MOD,FE_ULT_MOD,IP_ULT_MOD,VALOR5,EMPRESA_COD,VALOR6,VALOR7,OBSERVACION,VALOR8,VALOR9) values (DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,(SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'NC_MOTIVOS_ORDEN_SERVICIO'),'PARAMETRO QUE VALIDA CREACION DE NOTA DE CREDITO SEGUN MOTIVOS','N','Traslado cliente perdió equipos no desea pagar valor',null,null,'Activo','atarreaga',sysdate,'127.0.0.1',null,null,null,null,33,null,null,null,null,null);
Insert into DB_GENERAL.ADMI_PARAMETRO_DET (ID_PARAMETRO_DET,PARAMETRO_ID,DESCRIPCION,VALOR1,VALOR2,VALOR3,VALOR4,ESTADO,USR_CREACION,FE_CREACION,IP_CREACION,USR_ULT_MOD,FE_ULT_MOD,IP_ULT_MOD,VALOR5,EMPRESA_COD,VALOR6,VALOR7,OBSERVACION,VALOR8,VALOR9) values (DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,(SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'NC_MOTIVOS_ORDEN_SERVICIO'),'PARAMETRO QUE VALIDA CREACION DE NOTA DE CREDITO SEGUN MOTIVOS','N','Rechazo por solicitud del cliente',null,null,'Activo','atarreaga',sysdate,'127.0.0.1',null,null,null,null,33,null,null,null,null,null);
Insert into DB_GENERAL.ADMI_PARAMETRO_DET (ID_PARAMETRO_DET,PARAMETRO_ID,DESCRIPCION,VALOR1,VALOR2,VALOR3,VALOR4,ESTADO,USR_CREACION,FE_CREACION,IP_CREACION,USR_ULT_MOD,FE_ULT_MOD,IP_ULT_MOD,VALOR5,EMPRESA_COD,VALOR6,VALOR7,OBSERVACION,VALOR8,VALOR9) values (DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,(SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'NC_MOTIVOS_ORDEN_SERVICIO'),'PARAMETRO QUE VALIDA CREACION DE NOTA DE CREDITO SEGUN MOTIVOS','N','No existe autorización de administración',null,null,'Activo','atarreaga',sysdate,'127.0.0.1',null,null,null,null,33,null,null,null,null,null);
Insert into DB_GENERAL.ADMI_PARAMETRO_DET (ID_PARAMETRO_DET,PARAMETRO_ID,DESCRIPCION,VALOR1,VALOR2,VALOR3,VALOR4,ESTADO,USR_CREACION,FE_CREACION,IP_CREACION,USR_ULT_MOD,FE_ULT_MOD,IP_ULT_MOD,VALOR5,EMPRESA_COD,VALOR6,VALOR7,OBSERVACION,VALOR8,VALOR9) values (DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,(SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'NC_MOTIVOS_ORDEN_SERVICIO'),'PARAMETRO QUE VALIDA CREACION DE NOTA DE CREDITO SEGUN MOTIVOS','N','Domicilio/Edificio/Conjunto/CC sin Accesos',null,null,'Activo','atarreaga',sysdate,'127.0.0.1',null,null,null,null,33,null,null,null,null,null);
Insert into DB_GENERAL.ADMI_PARAMETRO_DET (ID_PARAMETRO_DET,PARAMETRO_ID,DESCRIPCION,VALOR1,VALOR2,VALOR3,VALOR4,ESTADO,USR_CREACION,FE_CREACION,IP_CREACION,USR_ULT_MOD,FE_ULT_MOD,IP_ULT_MOD,VALOR5,EMPRESA_COD,VALOR6,VALOR7,OBSERVACION,VALOR8,VALOR9) values (DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,(SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'NC_MOTIVOS_ORDEN_SERVICIO'),'PARAMETRO QUE VALIDA CREACION DE NOTA DE CREDITO SEGUN MOTIVOS','N','Demora en tiempo de instalación',null,null,'Activo','atarreaga',sysdate,'127.0.0.1',null,null,null,null,33,null,null,null,null,null);
Insert into DB_GENERAL.ADMI_PARAMETRO_DET (ID_PARAMETRO_DET,PARAMETRO_ID,DESCRIPCION,VALOR1,VALOR2,VALOR3,VALOR4,ESTADO,USR_CREACION,FE_CREACION,IP_CREACION,USR_ULT_MOD,FE_ULT_MOD,IP_ULT_MOD,VALOR5,EMPRESA_COD,VALOR6,VALOR7,OBSERVACION,VALOR8,VALOR9) values (DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,(SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'NC_MOTIVOS_ORDEN_SERVICIO'),'PARAMETRO QUE VALIDA CREACION DE NOTA DE CREDITO SEGUN MOTIVOS','N','Cliente no esta enlazado a caja de edificio',null,null,'Activo','atarreaga',sysdate,'127.0.0.1',null,null,null,null,33,null,null,null,null,null);
Insert into DB_GENERAL.ADMI_PARAMETRO_DET (ID_PARAMETRO_DET,PARAMETRO_ID,DESCRIPCION,VALOR1,VALOR2,VALOR3,VALOR4,ESTADO,USR_CREACION,FE_CREACION,IP_CREACION,USR_ULT_MOD,FE_ULT_MOD,IP_ULT_MOD,VALOR5,EMPRESA_COD,VALOR6,VALOR7,OBSERVACION,VALOR8,VALOR9) values (DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,(SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'NC_MOTIVOS_ORDEN_SERVICIO'),'PARAMETRO QUE VALIDA CREACION DE NOTA DE CREDITO SEGUN MOTIVOS','N','Solicita la instalación superior a los 7 días laborales',null,null,'Activo','atarreaga',sysdate,'127.0.0.1',null,null,null,null,33,null,null,null,null,null);
Insert into DB_GENERAL.ADMI_PARAMETRO_DET (ID_PARAMETRO_DET,PARAMETRO_ID,DESCRIPCION,VALOR1,VALOR2,VALOR3,VALOR4,ESTADO,USR_CREACION,FE_CREACION,IP_CREACION,USR_ULT_MOD,FE_ULT_MOD,IP_ULT_MOD,VALOR5,EMPRESA_COD,VALOR6,VALOR7,OBSERVACION,VALOR8,VALOR9) values (DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,(SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'NC_MOTIVOS_ORDEN_SERVICIO'),'PARAMETRO QUE VALIDA CREACION DE NOTA DE CREDITO SEGUN MOTIVOS','N','Edificio no liberado',null,null,'Activo','atarreaga',sysdate,'127.0.0.1',null,null,null,null,33,null,null,null,null,null);
Insert into DB_GENERAL.ADMI_PARAMETRO_DET (ID_PARAMETRO_DET,PARAMETRO_ID,DESCRIPCION,VALOR1,VALOR2,VALOR3,VALOR4,ESTADO,USR_CREACION,FE_CREACION,IP_CREACION,USR_ULT_MOD,FE_ULT_MOD,IP_ULT_MOD,VALOR5,EMPRESA_COD,VALOR6,VALOR7,OBSERVACION,VALOR8,VALOR9) values (DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,(SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'NC_MOTIVOS_ORDEN_SERVICIO'),'PARAMETRO QUE VALIDA CREACION DE NOTA DE CREDITO SEGUN MOTIVOS','N','Edificio no cumple con las políticas para liberación',null,null,'Activo','atarreaga',sysdate,'127.0.0.1',null,null,null,null,33,null,null,null,null,null);
Insert into DB_GENERAL.ADMI_PARAMETRO_DET (ID_PARAMETRO_DET,PARAMETRO_ID,DESCRIPCION,VALOR1,VALOR2,VALOR3,VALOR4,ESTADO,USR_CREACION,FE_CREACION,IP_CREACION,USR_ULT_MOD,FE_ULT_MOD,IP_ULT_MOD,VALOR5,EMPRESA_COD,VALOR6,VALOR7,OBSERVACION,VALOR8,VALOR9) values (DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,(SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'NC_MOTIVOS_ORDEN_SERVICIO'),'PARAMETRO QUE VALIDA CREACION DE NOTA DE CREDITO SEGUN MOTIVOS','N','Cliente no desea la instalación',null,null,'Activo','atarreaga',sysdate,'127.0.0.1',null,null,null,null,33,null,null,null,null,null);
Insert into DB_GENERAL.ADMI_PARAMETRO_DET (ID_PARAMETRO_DET,PARAMETRO_ID,DESCRIPCION,VALOR1,VALOR2,VALOR3,VALOR4,ESTADO,USR_CREACION,FE_CREACION,IP_CREACION,USR_ULT_MOD,FE_ULT_MOD,IP_ULT_MOD,VALOR5,EMPRESA_COD,VALOR6,VALOR7,OBSERVACION,VALOR8,VALOR9) values (DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,(SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'NC_MOTIVOS_ORDEN_SERVICIO'),'PARAMETRO QUE VALIDA CREACION DE NOTA DE CREDITO SEGUN MOTIVOS','N','Cliente mal asociado a Edificación/Conjunto/Urba',null,null,'Activo','atarreaga',sysdate,'127.0.0.1',null,null,null,null,33,null,null,null,null,null);
Insert into DB_GENERAL.ADMI_PARAMETRO_DET (ID_PARAMETRO_DET,PARAMETRO_ID,DESCRIPCION,VALOR1,VALOR2,VALOR3,VALOR4,ESTADO,USR_CREACION,FE_CREACION,IP_CREACION,USR_ULT_MOD,FE_ULT_MOD,IP_ULT_MOD,VALOR5,EMPRESA_COD,VALOR6,VALOR7,OBSERVACION,VALOR8,VALOR9) values (DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,(SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'NC_MOTIVOS_ORDEN_SERVICIO'),'PARAMETRO QUE VALIDA CREACION DE NOTA DE CREDITO SEGUN MOTIVOS','N','Edificio/Conjunto no se encuentra liberado',null,null,'Activo','atarreaga',sysdate,'127.0.0.1',null,null,null,null,33,null,null,null,null,null);
Insert into DB_GENERAL.ADMI_PARAMETRO_DET (ID_PARAMETRO_DET,PARAMETRO_ID,DESCRIPCION,VALOR1,VALOR2,VALOR3,VALOR4,ESTADO,USR_CREACION,FE_CREACION,IP_CREACION,USR_ULT_MOD,FE_ULT_MOD,IP_ULT_MOD,VALOR5,EMPRESA_COD,VALOR6,VALOR7,OBSERVACION,VALOR8,VALOR9) values (DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,(SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'NC_MOTIVOS_ORDEN_SERVICIO'),'PARAMETRO QUE VALIDA CREACION DE NOTA DE CREDITO SEGUN MOTIVOS','N','OT ha superado el tiempo comercial 48 Horas',null,null,'Activo','atarreaga',sysdate,'127.0.0.1',null,null,null,null,33,null,null,null,null,null);
Insert into DB_GENERAL.ADMI_PARAMETRO_DET (ID_PARAMETRO_DET,PARAMETRO_ID,DESCRIPCION,VALOR1,VALOR2,VALOR3,VALOR4,ESTADO,USR_CREACION,FE_CREACION,IP_CREACION,USR_ULT_MOD,FE_ULT_MOD,IP_ULT_MOD,VALOR5,EMPRESA_COD,VALOR6,VALOR7,OBSERVACION,VALOR8,VALOR9) values (DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,(SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'NC_MOTIVOS_ORDEN_SERVICIO'),'PARAMETRO QUE VALIDA CREACION DE NOTA DE CREDITO SEGUN MOTIVOS','N','Cliente no va a realizar la OBRA CIVIL',null,null,'Activo','atarreaga',sysdate,'127.0.0.1',null,null,null,null,33,null,null,null,null,null);
Insert into DB_GENERAL.ADMI_PARAMETRO_DET (ID_PARAMETRO_DET,PARAMETRO_ID,DESCRIPCION,VALOR1,VALOR2,VALOR3,VALOR4,ESTADO,USR_CREACION,FE_CREACION,IP_CREACION,USR_ULT_MOD,FE_ULT_MOD,IP_ULT_MOD,VALOR5,EMPRESA_COD,VALOR6,VALOR7,OBSERVACION,VALOR8,VALOR9) values (DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,(SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'NC_MOTIVOS_ORDEN_SERVICIO'),'PARAMETRO QUE VALIDA CREACION DE NOTA DE CREDITO SEGUN MOTIVOS','N','Cliente no acepta el pago de valores adicionales',null,null,'Activo','atarreaga',sysdate,'127.0.0.1',null,null,null,null,33,null,null,null,null,null);
Insert into DB_GENERAL.ADMI_PARAMETRO_DET (ID_PARAMETRO_DET,PARAMETRO_ID,DESCRIPCION,VALOR1,VALOR2,VALOR3,VALOR4,ESTADO,USR_CREACION,FE_CREACION,IP_CREACION,USR_ULT_MOD,FE_ULT_MOD,IP_ULT_MOD,VALOR5,EMPRESA_COD,VALOR6,VALOR7,OBSERVACION,VALOR8,VALOR9) values (DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,(SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'NC_MOTIVOS_ORDEN_SERVICIO'),'PARAMETRO QUE VALIDA CREACION DE NOTA DE CREDITO SEGUN MOTIVOS','N','NO EXISTE CONFIRMACIÓN DEL COBRO POR EXCEDENTES DE MATERIALES/OBRAS CIVILES',null,null,'Activo','atarreaga',sysdate,'127.0.0.1',null,null,null,null,33,null,null,null,null,null);
Insert into DB_GENERAL.ADMI_PARAMETRO_DET (ID_PARAMETRO_DET,PARAMETRO_ID,DESCRIPCION,VALOR1,VALOR2,VALOR3,VALOR4,ESTADO,USR_CREACION,FE_CREACION,IP_CREACION,USR_ULT_MOD,FE_ULT_MOD,IP_ULT_MOD,VALOR5,EMPRESA_COD,VALOR6,VALOR7,OBSERVACION,VALOR8,VALOR9) values (DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,(SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'NC_MOTIVOS_ORDEN_SERVICIO'),'PARAMETRO QUE VALIDA CREACION DE NOTA DE CREDITO SEGUN MOTIVOS','N','Formato de dirección incorrecta en zona regenerada',null,null,'Activo','atarreaga',sysdate,'127.0.0.1',null,null,null,null,33,null,null,null,null,null);
Insert into DB_GENERAL.ADMI_PARAMETRO_DET (ID_PARAMETRO_DET,PARAMETRO_ID,DESCRIPCION,VALOR1,VALOR2,VALOR3,VALOR4,ESTADO,USR_CREACION,FE_CREACION,IP_CREACION,USR_ULT_MOD,FE_ULT_MOD,IP_ULT_MOD,VALOR5,EMPRESA_COD,VALOR6,VALOR7,OBSERVACION,VALOR8,VALOR9) values (DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,(SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'NC_MOTIVOS_ORDEN_SERVICIO'),'PARAMETRO QUE VALIDA CREACION DE NOTA DE CREDITO SEGUN MOTIVOS','N','Autorizacion de la Administracion del Edificio/Urbanizacion',null,null,'Activo','atarreaga',sysdate,'127.0.0.1',null,null,null,null,33,null,null,null,null,null);
Insert into DB_GENERAL.ADMI_PARAMETRO_DET (ID_PARAMETRO_DET,PARAMETRO_ID,DESCRIPCION,VALOR1,VALOR2,VALOR3,VALOR4,ESTADO,USR_CREACION,FE_CREACION,IP_CREACION,USR_ULT_MOD,FE_ULT_MOD,IP_ULT_MOD,VALOR5,EMPRESA_COD,VALOR6,VALOR7,OBSERVACION,VALOR8,VALOR9) values (DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,(SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'NC_MOTIVOS_ORDEN_SERVICIO'),'PARAMETRO QUE VALIDA CREACION DE NOTA DE CREDITO SEGUN MOTIVOS','N','Solicitud de instalación supera el tiempo de espera establecido',null,null,'Activo','atarreaga',sysdate,'127.0.0.1',null,null,null,null,33,null,null,null,null,null);
Insert into DB_GENERAL.ADMI_PARAMETRO_DET (ID_PARAMETRO_DET,PARAMETRO_ID,DESCRIPCION,VALOR1,VALOR2,VALOR3,VALOR4,ESTADO,USR_CREACION,FE_CREACION,IP_CREACION,USR_ULT_MOD,FE_ULT_MOD,IP_ULT_MOD,VALOR5,EMPRESA_COD,VALOR6,VALOR7,OBSERVACION,VALOR8,VALOR9) values (DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,(SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'NC_MOTIVOS_ORDEN_SERVICIO'),'PARAMETRO QUE VALIDA CREACION DE NOTA DE CREDITO SEGUN MOTIVOS','N','Cliente confirma dirección incorrecta',null,null,'Activo','atarreaga',sysdate,'127.0.0.1',null,null,null,null,33,null,null,null,null,null);
Insert into DB_GENERAL.ADMI_PARAMETRO_DET (ID_PARAMETRO_DET,PARAMETRO_ID,DESCRIPCION,VALOR1,VALOR2,VALOR3,VALOR4,ESTADO,USR_CREACION,FE_CREACION,IP_CREACION,USR_ULT_MOD,FE_ULT_MOD,IP_ULT_MOD,VALOR5,EMPRESA_COD,VALOR6,VALOR7,OBSERVACION,VALOR8,VALOR9) values (DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,(SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'NC_MOTIVOS_ORDEN_SERVICIO'),'PARAMETRO QUE VALIDA CREACION DE NOTA DE CREDITO SEGUN MOTIVOS','N','Teléfonos incorrectos',null,null,'Activo','atarreaga',sysdate,'127.0.0.1',null,null,null,null,33,null,null,null,null,null);
Insert into DB_GENERAL.ADMI_PARAMETRO_DET (ID_PARAMETRO_DET,PARAMETRO_ID,DESCRIPCION,VALOR1,VALOR2,VALOR3,VALOR4,ESTADO,USR_CREACION,FE_CREACION,IP_CREACION,USR_ULT_MOD,FE_ULT_MOD,IP_ULT_MOD,VALOR5,EMPRESA_COD,VALOR6,VALOR7,OBSERVACION,VALOR8,VALOR9) values (DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,(SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'NC_MOTIVOS_ORDEN_SERVICIO'),'PARAMETRO QUE VALIDA CREACION DE NOTA DE CREDITO SEGUN MOTIVOS','N','Cliente no contesta a los números registrados en el sistema',null,null,'Activo','atarreaga',sysdate,'127.0.0.1',null,null,null,null,33,null,null,null,null,null);
Insert into DB_GENERAL.ADMI_PARAMETRO_DET (ID_PARAMETRO_DET,PARAMETRO_ID,DESCRIPCION,VALOR1,VALOR2,VALOR3,VALOR4,ESTADO,USR_CREACION,FE_CREACION,IP_CREACION,USR_ULT_MOD,FE_ULT_MOD,IP_ULT_MOD,VALOR5,EMPRESA_COD,VALOR6,VALOR7,OBSERVACION,VALOR8,VALOR9) values (DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,(SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'NC_MOTIVOS_ORDEN_SERVICIO'),'PARAMETRO QUE VALIDA CREACION DE NOTA DE CREDITO SEGUN MOTIVOS','S','Demora en la instalación',null,null,'Activo','atarreaga',sysdate,'127.0.0.1',null,null,null,null,33,null,null,null,null,null);
Insert into DB_GENERAL.ADMI_PARAMETRO_DET (ID_PARAMETRO_DET,PARAMETRO_ID,DESCRIPCION,VALOR1,VALOR2,VALOR3,VALOR4,ESTADO,USR_CREACION,FE_CREACION,IP_CREACION,USR_ULT_MOD,FE_ULT_MOD,IP_ULT_MOD,VALOR5,EMPRESA_COD,VALOR6,VALOR7,OBSERVACION,VALOR8,VALOR9) values (DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,(SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'NC_MOTIVOS_ORDEN_SERVICIO'),'PARAMETRO QUE VALIDA CREACION DE NOTA DE CREDITO SEGUN MOTIVOS','S','Venta incorrecta',null,null,'Activo','atarreaga',sysdate,'127.0.0.1',null,null,null,null,33,null,null,null,null,null);
Insert into DB_GENERAL.ADMI_PARAMETRO_DET (ID_PARAMETRO_DET,PARAMETRO_ID,DESCRIPCION,VALOR1,VALOR2,VALOR3,VALOR4,ESTADO,USR_CREACION,FE_CREACION,IP_CREACION,USR_ULT_MOD,FE_ULT_MOD,IP_ULT_MOD,VALOR5,EMPRESA_COD,VALOR6,VALOR7,OBSERVACION,VALOR8,VALOR9) values (DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,(SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'NC_MOTIVOS_ORDEN_SERVICIO'),'PARAMETRO QUE VALIDA CREACION DE NOTA DE CREDITO SEGUN MOTIVOS','S','No autoriza el uso de su cobre',null,null,'Activo','atarreaga',sysdate,'127.0.0.1',null,null,null,null,33,null,null,null,null,null);
Insert into DB_GENERAL.ADMI_PARAMETRO_DET (ID_PARAMETRO_DET,PARAMETRO_ID,DESCRIPCION,VALOR1,VALOR2,VALOR3,VALOR4,ESTADO,USR_CREACION,FE_CREACION,IP_CREACION,USR_ULT_MOD,FE_ULT_MOD,IP_ULT_MOD,VALOR5,EMPRESA_COD,VALOR6,VALOR7,OBSERVACION,VALOR8,VALOR9) values (DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,(SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'NC_MOTIVOS_ORDEN_SERVICIO'),'PARAMETRO QUE VALIDA CREACION DE NOTA DE CREDITO SEGUN MOTIVOS','S','Cliente tiene otro proveedor',null,null,'Activo','atarreaga',sysdate,'127.0.0.1',null,null,null,null,33,null,null,null,null,null);
Insert into DB_GENERAL.ADMI_PARAMETRO_DET (ID_PARAMETRO_DET,PARAMETRO_ID,DESCRIPCION,VALOR1,VALOR2,VALOR3,VALOR4,ESTADO,USR_CREACION,FE_CREACION,IP_CREACION,USR_ULT_MOD,FE_ULT_MOD,IP_ULT_MOD,VALOR5,EMPRESA_COD,VALOR6,VALOR7,OBSERVACION,VALOR8,VALOR9) values (DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,(SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'NC_MOTIVOS_ORDEN_SERVICIO'),'PARAMETRO QUE VALIDA CREACION DE NOTA DE CREDITO SEGUN MOTIVOS','S','Ya no desea el servicio',null,null,'Activo','atarreaga',sysdate,'127.0.0.1',null,null,null,null,33,null,null,null,null,null);
Insert into DB_GENERAL.ADMI_PARAMETRO_DET (ID_PARAMETRO_DET,PARAMETRO_ID,DESCRIPCION,VALOR1,VALOR2,VALOR3,VALOR4,ESTADO,USR_CREACION,FE_CREACION,IP_CREACION,USR_ULT_MOD,FE_ULT_MOD,IP_ULT_MOD,VALOR5,EMPRESA_COD,VALOR6,VALOR7,OBSERVACION,VALOR8,VALOR9) values (DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,(SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'NC_MOTIVOS_ORDEN_SERVICIO'),'PARAMETRO QUE VALIDA CREACION DE NOTA DE CREDITO SEGUN MOTIVOS','S','Incumplimiento ofrecimiento Comercial',null,null,'Activo','atarreaga',sysdate,'127.0.0.1',null,null,null,null,33,null,null,null,null,null);


--Actualizacion de numeracion facturas oficina ecuanet guayaquil

UPDATE DB_comercial.admi_numeracion
set numeracion_uno = '099',
numeracion_dos = '011'
where codigo = 'FACE'
and oficina_id = (select ID_OFICINA from DB_COMERCIAL.INFO_OFICINA_GRUPO 
WHERE NOMBRE_OFICINA='ECUANET - GUAYAQUIL' AND ESTADO='Activo' AND EMPRESA_ID=33);

--Actualizacion de numeracion facturas oficina ecuanet quito

UPDATE DB_comercial.admi_numeracion
set numeracion_uno = '098',
numeracion_dos = '011'
where codigo = 'FACE'
and oficina_id = (select ID_OFICINA from DB_COMERCIAL.INFO_OFICINA_GRUPO 
WHERE NOMBRE_OFICINA='ECUANET - QUITO' AND ESTADO='Activo' AND EMPRESA_ID=33);

--Actualizacion de numeracion Notas de credito

UPDATE DB_comercial.admi_numeracion
set numeracion_uno = '098',
numeracion_dos = '011'
where codigo = 'NCE'
and oficina_id = (select ID_OFICINA from DB_COMERCIAL.INFO_OFICINA_GRUPO 
WHERE NOMBRE_OFICINA='ECUANET - QUITO' AND ESTADO='Activo' AND EMPRESA_ID=33);


COMMIT;
/