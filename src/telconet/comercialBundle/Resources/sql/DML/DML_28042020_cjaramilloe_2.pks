/**
 *
 * Actualización de plantillas TM Comercial
 *
 * @author Christian Jaramillo Espinoza <cjaramilloe@telconet.ec>
 * @version 1.0 28-04-2020
 * 
 **/

SET DEFINE OFF;

DECLARE
    registro_contrato      DB_FIRMAELECT.ADM_EMPRESA_PLANTILLA%ROWTYPE;
    registro_contrato_sd   DB_FIRMAELECT.ADM_EMPRESA_PLANTILLA%ROWTYPE;
    registro_formulario_sd DB_FIRMAELECT.ADM_EMPRESA_PLANTILLA%ROWTYPE;
    registro_pagare        DB_FIRMAELECT.ADM_EMPRESA_PLANTILLA%ROWTYPE;
    registro_debito        DB_FIRMAELECT.ADM_EMPRESA_PLANTILLA%ROWTYPE;
    registro_adendum       DB_FIRMAELECT.ADM_EMPRESA_PLANTILLA%ROWTYPE;
  
    plantilla_editada_contrato      CLOB := '<!DOCTYPE html>';
    plantilla_editada_contrato_sd   CLOB;
    plantilla_editada_form_sd       CLOB;
    plantilla_editada_pagare        CLOB;
    plantilla_editada_debito        CLOB;
    plantilla_editada_adendum       CLOB := '<!DOCTYPE html>';
BEGIN
    
    ------- CONTRATO ----- 
    DBMS_LOB.APPEND(plantilla_editada_contrato, '
	 <html>
	 <head>
		 <title>Contrato Digital - Netlife</title>
		 <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
		 <style type="text/css">
			 *{
				 font-family: "Utopia";
			 }
			 body
			 {
				 width: 950px;
				 font-size:11px;
			 }
			 #bienvenido
			 {
				 font-weight: bold;
				 font-size:16px;
				 position: absolute;
			 }
			 #netlife
			 {
				 font-size:9px;
			 }
 
			 /* // ==========================================
				// Clases definidas para los componentes
				// ==========================================*/
			 #contenedor {
				 display: table;
				 width: auto;
				 vertical-align: middle;
				 border-spacing: 0px;
				 margin: 0px;
				 padding: 0px;
			 }
			 #row {
				 display: table-row;
				 vertical-align: middle;
			 }
 
			 #col {
				 display: inline-block;
				 vertical-align: middle;
			 }
			 #colCell {
				 display: table-cell;
				 vertical-align: middle;
			 }
			 /* // ==========================================
				// Clases definidas para los componentes
				// ==========================================*/
			 .labelBlock
			 {
				 font-weight: bold;
				 background: #f9e314;
				 font-size:12px;
				 border-top: 1px black solid;
				 border-bottom: 1px solid black;
				 margin: 1em 0;
				 padding-left: 1em;
			 }
			 label,.labelGris
			 {
				 background: #E6E6E6;
				 border-radius: 3px;
				 -moz-border-radius: 2px ;
				 -webkit-border-radius: 2px ;
			 }
			 .box{
				 height: 15px;
				 width: 15px;
				 border: 1px solid black;
				 display:inline-block;
				 border-radius: 2px;
				 -moz-border-radius: 2px ;
				 -webkit-border-radius: 2px ;
				 vertical-align: top;
				 text-align: center;
			 }
			 .box-label{
				 padding-left: 3px;
				 text-align: center;
				 display:inline-block;
				 vertical-align: top;
			 }
			 .line-height,.labelBlock,#col{
				 height: 18px;
				 line-height: 18px;
				 margin-top: 2px;
			 }
			 .textLeft{
				 text-align: left;
			 }
			 .textRight{
				 text-align: right;
			 }
			 .textCenter{
				 text-align: center;
			 }
			 .textPadding{
				 padding-left: 5px;
			 }
			 .borderTable th,.borderTable td {
				 border: 1px solid black;
			 }
 
			 /* // ==========================================
				// Vi&ntilde;etas para las clausulas
				// ==========================================*/
			 .clausulas ul {
				 list-style: none; /* Remove list bullets */
				 padding: 0;
				 margin: 0;
			 }
 
			 .clausulas li {
				 padding-left: 16px;
			 }
 
			 .clausulas li:before {
				 content: "-";
				 padding-right: 5px;
			 }/* // ==========================================
				// Clases de manejo de tama&ntilde;o de columnas
				// ==========================================*/
 
			 .col-width-5{
				 width: 5% !important;
			 }
			 .col-width-10{
				 width: 10% !important;
			 }
			 .col-width-15{
				 width: 15% !important;
			 }
			 .col-width-20{
				 width: 20% !important;
			 }
			 .col-width-25{
				 width: 25% !important;
			 }
			 .col-width-30{
				 width: 30% !important;
			 }
			 .col-width-35{
				 width: 35% !important;
			 }
			 .col-width-40{
				 width: 40% !important;
			 }
			 .col-width-45{
				 width: 45% !important;
			 }
			 .col-width-50{
				 width: 50% !important;
			 }
			 .col-width-55{
				 width: 55% !important;
			 }
			 .col-width-60{
				 width: 60% !important;
			 }
			 .col-width-65{
				 width: 65% !important;
			 }
			 .col-width-70{
				 width: 70% !important;
			 }
			 .col-width-75{
				 width: 75% !important;
			 }
			 .col-width-80{
				 width: 80% !important;
			 }
			 .col-width-85{
				 width: 85% !important;
			 }
			 .col-width-90{
				 width: 90% !important;
			 }
			 .col-width-95{
				 width: 95% !important;
			 }
			 .col-width-100{
				 width: 100% !important;
			 }
			 a {
				 display: block;
			 }
		 </style>
	 </head> <body>
		 <!-- ================================= -->
		 <!-- Logo Netlife y numero de contrato -->
		 <!-- ================================= -->
		<div id="contenedor" class="col-width-100" style="font-size:14px;">
		 <table class="col-width-100">         
		   <tr>     
			 <td class="col-width-75" style="font-size:14px;"><b>CONTRATO DE ADHESI&Oacute;N DE PRESTACI&Oacute;N DE SERVICIOS DE ACCESO A INTERNET/PORTADOR</b></td> 
			 <td id="netlife" class="col-width-25" align="center" rowspan="4">
			   <img src="http://images.telconet.net/others/telcos/logo_netlife.png" alt="log" title="NETLIFE" height="40"/>
			   <div style="font-size:14px">3920000 &oacute; al <br/>1-700 NETLIFE (638-543)</div>
			   <div style="font-size:20px"><b>$!numeroAdendum</b></div>
			 </td>
		   </tr>
		   <tr style="$!verNumeroAdendum">
			 <td class="col-width-75" style="font-size:14px;">Este documento adenda al contrato $!numeroContrato</td>
		   </tr>
		 </table>
	   </div>
		 <!-- ============================ -->
		 <!-- Datos iniciales del Contrato -->
		 <!-- ============================ -->
		 <div style="clear: both;"></div>
		 <div id="contenedor" class="col-width-60">
			  <div id="row">
				 <div id="col" class="col-width-30">
					 <b>CONTRATO: </b>
				 </div>
				 <div id="col" class="col-width-15">
					 <div class="box">$!isNuevo</div>
					 <div class="box-label">Nuevo</div>
				 </div>
				 <div id="col" class="col-width-15">
					 <div class="box">$!isExistente</div>
					 <div class="box-label">Existente</div>
				 </div>
			 </div>
			 <div id="row">
				 <div id="col" class="col-width-30">
					 <b>FECHA(aa-mm-dd): </b>
					 </div>
				 <div id="col">$fechaActual</div>
			 </div>
			 <div id="row">
				 <div id="col" class="col-width-30">
					 <b>TIPO DE CLIENTE:</b>
				 </div>
				 <div id="col" class="col-width-15">
					 <div class="box">$!isNatural</div>
					 <div class="box-label">Natural</div>
				 </div>
				 <div id="col" class="col-width-15">
					 <div class="box">$!isJuridico</div>
					 <div class="box-label">Juridico</div>
				 </div>
			 </div>
		 </div>
 
		 <!-- ========================================================= -->
		 <!--        Datos de Adhesión de prestación de servicios       -->
		 <!-- ========================================================= -->
		 <div style="clear: both;"></div>
		 <div class="labelBlock">CONTRATO DE ADHESI&Oacute;N DE PRESTACI&Oacute;N DE SERVICIOS DE ACCESO A INTERNET/PORTADOR</div>
		 <div>
			 <span style="font-size:11px">
				 <b>Primera:</b> En la ciudad de $!ciudadServicio a los $!diaActual del mes de $!mesActual Celebran el presente Contrato de Adhesi&oacute;n de Prestaci&oacute;n de Servicios de Acceso a
				 Internet/Portador; 1) por una parte MEGADATOS S.A., compañ&iacute;a constituida bajo las leyes de la Rep&uacute;blica del Ecuador, cuyo objeto social constituye entre
				 otros, la prestaci&oacute;n de servicios de telcomunicaciones. Mediante resolución SNT-2010-085 del 30 de marzo del 2010 se autoriz&oacute; la renovaci&oacute;n del
				 permiso para la prestaci&oacute;n del servicio de valor agregado de acceso a la red de internet, permiso suscrito el 8 de abril del 2010 e inscrito en el tomo 8S a
				 fojas 8503 del registro p&uacute;blico de telecomunicaciones, cuyo nombre Comercial es NETLIFE 1. en adelante denominado simplemente MEGADATOS, cuyo
				 nombre comercial es NETLIFE, ubicada en la calle N&uacute;ñez de Vela y Atahualpa-Torre del Puente, en la provincia de Pichincha, cant&oacute;n Quito, ciudad de
				 Quito, Parroquia Iñaquito, Tel&eacute;fonos: 023920000, RUC: 1791287541001, mail: info@netlife.net.ec, web:www.netlife.ec/puntos-de-atencion/ 2) por otra
				 parte el ABONADO, cuyos datos se datallan a contiuaci&oacute;n:
			 </span>
		 </div>
 
		 <!-- ============================== -->
		 <!--        Datos del Cliente       -->
		 <!-- ============================== -->
		 <div style="clear: both;"></div>
		 <div class="labelBlock">DATOS DEL ABONADO/SUSCRIPTOR</div>
		 <div id="contenedor" class="col-width-100" >
			 <div id="row">
				 <div id="col" class="col-width-15"><b>Nombre Completos:</b></div>
				 <div id="col" class="col-width-45 labelGris">
					 $!nombresApellidos
					 <span class="textPadding"></span>
				 </div>
				 <div id="col" class="col-width-5"></div>
				 <div id="col" class="col-width-5"><b>CC:</b></div>
				 <div id="col" class="col-width-30 labelGris">
					 <span class="textPadding">
					 $!identificacion
					 </span>
				 </div>
			 </div><div id="row">
				 <div id="col" class="col-width-15"><b>Nacionalidad:</b></div>
				 <div id="col" class="col-width-15 labelGris">
					 <span class="textPadding">
					 #if ($nacionalidad == "NAC")
						 ECUATORIANA
					 #elseif ($userType == "EXT")
						 EXTRANJERA
					 #else
						 $!nacionalidad
					 #end
					 </span>
				 </div>
				 <div id="col" class="col-width-15" style="text-align : center; padding-right: 1px;"><b>Estado Civil:</b></div>
				 <div id="col" class="col-width-15 labelGris">
					 <span class="textPadding">$!estadoCivil</span>
				 </div>
				 <div id="col" class="col-width-5"></div>
				 <div id="col" class="col-width-5" style="text-align : left; padding-right: 1px;"><b>Sexo:</b></div>
				 <div id="col" class="col-width-5" style="text-align : right; padding-right: 1px;"><b>M</b></div>
				 <div class="box">$!isMasculino</div>
				 <div id="col" class="col-width-5" style="text-align : right; padding-right: 1px;"><b>F</b></div>
				 <div class="box">$!isFemenino</div>
			 </div>
			 <div id="row">
				 <div id="col" class="col-width-60"><b>¿El abonado es discapacitado (en caso de afirmativo, aplica tarifa preferencial):</b></div>
				 <div id="col" class="col-width-5" style="text-align : right; padding-right: 1px;"><b>Si</b></div>
				 <div class="box">$!isDiscapacitadoSi</div>
				 <div id="col" class="col-width-5" style="text-align : right; padding-right: 1px;"><b>No</b></div>
				 <div class="box">$!isDiscapacitadoNo</div>
			 </div>
			 <div id="row">
				 <div id="col" class="col-width-15"><b>Razon Social:</b></div>
				 <div id="col" class="col-width-45  ">
					 <span class="textPadding">$!razonSocial</span>
				 </div>
				 <div id="col" class="col-width-5"></div>
				 <div id="col" class="col-width-5"><b>RUC:</b></div>
				 <div id="col" class="col-width-30 labelGris">
					 <span class="textPadding">$!ruc</span>
				 </div>
			 </div>
			 <div id="row">
				 <div id="col" class="col-width-15"><b>Representante Legal:</b></div>
				 <div id="col" class="col-width-45 labelGris">
					 <span class="textPadding">$!representanteLegal</span>
				 </div>
				 <div id="col" class="col-width-5"></div>
				 <div id="col" class="col-width-5"><b>CC:</b></div>
				 <div id="col" class="col-width-30 labelGris">
					 <span class="textPadding">$!ciRepresentanteLegal</span>
				 </div>
			 </div>
			 <div id="row">
				 <div id="col" class="col-width-15"><b>Actividad Econ&oacute;mica:</b></div>
				 <div id="col" class="col-width-45 labelGris">
					 <span class="textPadding">$!actividadEconomica</span>
				 </div>
				 <div id="col" class="col-width-5"></div>
				 <div id="col" class="col-width-10"><b>Origen Ingresos:</b></div>
				 <div id="col" class="col-width-25 labelGris">
					 <span class="textPadding">$!origenIngresos</span>
				 </div>
			 </div>
		 </div>
 
		 <!-- ======================================== -->
		 <!--        Datos del Cliente - Ubicacion     -->
		 <!-- ======================================== -->        <div style="clear: both;"></div>
		 <div id="contenedor" class="col-width-100" >
			 <div id="row">
				 <div id="col" class="col-width-20"></div>
				 <div id="col" class="col-width-80" style="font-style: oblique; padding-top: 5px;">Formato: Calle Principal,Numeraci&oacute;n,Calle Secundaria,Nombre Edficio o Conjunto,Piso,Numero
				 de Departamento o Casa</div>
			 </div>
			 <div id="row">
				 <div id="col" style="width:17%">
					 <b>Direcci&oacute;n estado de cuenta: </b>
					 </div>
				 <div id="col" style="width:83%" class="labelGris">
					 <span class="textPadding">$direccion</span>
				 </div>
			 </div>
			 <div id="row">
				 <div id="col" class="col-width-10"> <b>Referencia: </b> </div>
				 <div id="col" class="col-width-55 labelGris">
					 <span class="textPadding">$!referenciaServicio</span>
				 </div>
				 <div id="col" class="col-width-5"></div>
				 <div id="col" class="col-width-15"> <b>Coordenada Latitud: </b> </div>
				 <div id="col" class="col-width-15 labelGris">
					 <span class="textPadding">$latitud</span>
				 </div>
			 </div>
			 <div id="row">
				 <div id="col" class="col-width-10"> <b>Provincia: </b> </div>
				 <div id="col" class="col-width-20 labelGris">
					 <span class="textPadding">$provincia</span>
				 </div>
				 <div id="col" class="col-width-5"></div>
				 <div id="col" class="col-width-10"> <b>Ciudad: </b> </div>
				 <div id="col" class="col-width-20 labelGris">
					 <span class="textPadding">$ciudad</span>
				 </div>
 
				 <div id="col" class="col-width-5"></div>
				 <div id="col" class="col-width-15"> <b>Coordenada Longitud: </b> </div>
				 <div id="col" class="col-width-15 labelGris">
					 <span class="textPadding">$longuitud</span>
				 </div>
			 </div>
			 <div id="row">
				 <div id="col" class="col-width-10"> <b>Cant&oacute;n: </b> </div>
				 <div id="col" class="col-width-20 labelGris">
					 <span class="textPadding">$canton</span>
				 </div>
				 <div id="col" class="col-width-5"></div>
				 <div id="col" class="col-width-10"> <b>Parroquia: </b> </div>
				 <div id="col" class="col-width-20 labelGris">
					 <span class="textPadding">$parroquia</span>
				 </div>
				 <div id="col" class="col-width-5"></div>
				 <div id="col" class="col-width-10"><b>Sector/Barrio: </b> </div>
				 <div id="col" class="col-width-20 labelGris">
					 <span class="textPadding">$sector</span>
				 </div>
			 </div>
			 <div id="row">
				 <div id="col" class="col-width-10"> <b>Tipo Ubicaci&oacute;n: </b> </div>
				 <div id="col" class="col-width-45">
					 <div class="box-label">Casa</div>
					 <div class="box">$isCasa</div>
 
					 <div class="box-label">Edificio</div>
					 <div class="box">$isEdificio</div>
 
					 <div class="box-label">Conjunto</div>
					 <div class="box">$isConjunto</div>
				 </div>
				 <div id="col" class="col-width-10"> <b>Correo: </b> </div>
				 <div id="col" class="col-width-35 labelGris">
					 <span class="textPadding">$correoCliente</span>
				 </div>
			 </div>
			 <div id="row">
				 <div id="col" class="col-width-20"> <b>Tel&eacute;fono: </b> </div>
				 <div id="col" class="col-width-30 labelGris">
					 <span class="textPadding">$telefonoCliente</span>
				 </div>
				 <div id="col" class="col-width-5"></div>
				 <div id="col" class="col-width-10"> <b>Celular: </b> </div>
				 <div id="col" class="col-width-35 labelGris">
					 <span class="textPadding">$celularCliente</span>
				 </div>
			 </div>
			 <div id="row">
				 <div id="col" class="col-width-20"> <b>Nombre de Referencia Familiar: </b> </div>
				 <div id="col" class="col-width-30 labelGris">
					 <span class="textPadding">$refFamiliar1</span>
				 </div>
				 <div id="col" class="col-width-5"></div>
				 <div id="col" class="col-width-10"> <b>Tel&eacute;fono: </b> </div>
				 <div id="col" class="col-width-35 labelGris">
					 <span class="textPadding">$telefonoFamiliar1</span>
				 </div>
			 </div>
			 <div id="row">
				 <div id="col" class="col-width-20"> <b>Nombre de Referencia 2: </b> </div>
				 <div id="col" class="col-width-30 labelGris">
					 <span class="textPadding">$refFamiliar2</span>
				 </div>
				 <div id="col" class="col-width-5"></div>
				 <div id="col" class="col-width-10"> <b>Tel&eacute;fono: </b> </div>
				 <div id="col" class="col-width-35 labelGris">
					 <span class="textPadding">$telefonoFamiliar2</span>
				 </div>
			 </div>
			 <div id="row">
				 <div id="col" class="col-width-20"> <b>Nombre de Vendedor: </b> </div>
				 <div id="col" class="col-width-30 labelGris">
					 <span class="textPadding">$nombreVendedor</span>
				 </div>
				 <div id="col" class="col-width-5"></div>
				 <div id="col" class="col-width-10"> <b>C&oacute;digo: </b> </div>
				 <div id="col" class="col-width-35 labelGris">
					 <span class="textPadding">$codigoVendedor</span>
				 </div>
			 </div>
		 </div>
		 <br/>
 
		 <div>
			 <span style="font-size:11px">
				 <b>SEGUNDA.-</b> PRESTACI&Oacute;N DEL SERVICIO: MEGADATOS se compromete a proporcionar al ABONADO el acceso a redes nacionales e internacionales de
				 Internet de manera que el mismo disfrute de los servicios y funciones prestados por dichas redes. Se deja expresa constancia que MEGADATOS se
				 responsabiliza &uacute;nica y exclusivamente del acceso a las redes de Internet, por &eacute;ste motivo no resulta de su responsabilidad el contenido de la informaci&oacute;n a
				 la que pueda accederse, ni el almacenamiento de la misma, incluido el correo electr&oacute;nico. Las caracter&iacute;sticas del servicio objeto de este contrato, as&iacute; como
				 las caracter&oacute;sticas m&iacute;nimas que requiere el equipo y otros que deben ser garantizados por el ABONADO constan en el anverso de este contrato.
			 </span>
		 </div>
		 <!-- ======================================== -->
		 <!--        Datos del Servicio                -->
		 <!-- ======================================== -->
		 <br/>        <div style="clear: both;"></div>
		 <div class="labelBlock">DATOS DEL SERVICIO</div>
		 <div id="contenedor" class="col-width-100" >
			 <div id="row">
				 <div id="col" class="col-width-35">&iquest;Los datos de instalaci&oacute;n son los mismos que los datos del cliente?</div>
				 <div id="col" class="col-width-20">
					 <div class="box-label">Si</div>
					 <div class="box">$!isSi</div>
 
					 <div class="box-label">No</div>
					 <div class="box">$!isNo</div>
				 </div>
			 </div>
			 <div id="row">
				 <div id="col" class="col-width-20"></div>
				 <div id="col" class="col-width-80" style="font-style: oblique; padding-top: 5px;">Formato: Calle Principal,Numeraci&oacute;n,Calle Secundaria,Nombre Edficio o Conjunto,Piso,Numero
				 de Departamento o Casa</div>
			 </div>
			 <div id="row">
				 <div id="col" style="width:17%">
					 <b>Direcci&oacute;n estado de cuenta: </b>
					 </div>
				 <div id="col" style="width:83%" class="labelGris">
					 <span class="textPadding">$!direccionServicio</span>
				 </div>
			 </div>
			 <div id="row">
				 <div id="col" class="col-width-10"> <b>Referencia: </b> </div>
				 <div id="col" class="col-width-55 labelGris">
					 <span class="textPadding">$!referenciaServicio</span>
				 </div>
				 <div id="col" class="col-width-5"></div>
				 <div id="col" class="col-width-15"> <b>Coordenada Latitud: </b> </div>
				 <div id="col" class="col-width-15 labelGris">
					 <span class="textPadding">$!latitudServicio</span>
				 </div>
			 </div>
			 <div id="row">
				 <div id="col" class="col-width-10"> <b>Ciudad: </b> </div>
				 <div id="col" class="col-width-15 labelGris">
					 <span class="textPadding">$!ciudadServicio</span>
				 </div>
				 <div id="col" class="col-width-5"></div>
				 <div id="col" class="col-width-10"> <b>Canton: </b> </div>
				 <div id="col" class="col-width-25 labelGris">
					 <span class="textPadding">$!cantonServicio</span>
				 </div>
				 <div id="col" class="col-width-5"></div>
				 <div id="col" class="col-width-15"> <b>Coordenada Longitud: </b> </div>
				 <div id="col" class="col-width-15 labelGris">
					 <span class="textPadding">$!longuitudServicio</span>
				 </div>
			 </div>
			 <div id="row">
				 <div id="col" class="col-width-10"> <b>Parroquia: </b> </div>
				 <div id="col" class="col-width-20 labelGris">
					 <span class="textPadding">$!parroquiaServicio</span>
				 </div>
				 <div id="col" class="col-width-25"></div>
				 <div id="col" class="col-width-10"><b>Sector/Barrio: </b> </div>
				 <div id="col" class="col-width-35 labelGris">
					 <span class="textPadding">$!sectorServicio</span>
				 </div>
			 </div>
			 <div id="row">
				 <div id="col" class="col-width-10"> <b>Tipo Ubicaci&oacute;n: </b> </div>
				 <div id="col" class="col-width-45">
					 <div class="box-label">Casa</div>
					 <div class="box">$!casaServicio</div>
 
					 <div class="box-label">Edifcio</div>
					 <div class="box">$!edificioServicio</div>
 
					 <div class="box-label">Conjunto</div>
					 <div class="box">$!conjuntoServicio</div>
				 </div>
				 <div id="col" class="col-width-10"> <b>Correo: </b> </div>
				 <div id="col" class="col-width-35 labelGris">
					 <span class="textPadding">$!correoContacto</span>
				 </div>
			 </div>
			 <div id="row">
				 <div id="col" class="col-width-15"> <b>Tlf sitio: </b> </div>
				 <div id="col" class="col-width-35 labelGris">
					 <span class="textPadding">$!telefonoContacto</span>
				 </div>
				 <div id="col" class="col-width-5"></div>
				 <div id="col" class="col-width-10"> <b>Celular: </b> </div>
				 <div id="col" class="col-width-35 labelGris">
					 <span class="textPadding">$!celularContacto</span>
				 </div>
			 </div>
			 <div id="row">
				 <div id="col" class="col-width-15"> <b>Persona a contactar: </b> </div>
				 <div id="col" class="col-width-35 labelGris">
					 <span class="textPadding">$!personaContacto</span>
				 </div>
				 <div id="col" class="col-width-5"></div>
				 <div id="col" class="col-width-10"> <b>Horario: </b> </div>
				 <div id="col" class="col-width-35 labelGris">
					 <span class="textPadding">$!horarioContacto</span>
				 </div>
			 </div>
		 </div>');
    
        DBMS_LOB.APPEND(plantilla_editada_contrato,'
        <!-- ============================== -->
        <!--      Servicios Contratados     -->
        <!-- ============================== -->
        <div style="clear: both;"><br/><br/></div>
        <div class="labelBlock">SERVICIOS CONTRATADOS (ANEXO T&Eacute;CNICO)</div>
        <div style="width:36%; float:left; vertical-align:top;" >
            <div class="labelBlock textCenter" style="margin: 0; border:1px black solid;">CARACTER&Iacute;STICAS DEL PLAN</div>
            <table class="box-section-content col-width-100 borderTable" style="border-collapse:collapse;border-spacing:0;">
                <tr>
                    <td class="col-width-60 line-height" colspan="2">
                         COMPARTICI&Oacute;N:

                         <div class="box">$!is2a1</div>2:1 <div class="box">$!is1a1</div>1:1

                    </td>
                    <td class="col-width-50 line-height textCenter" colspan="2">
                        <div class="box">$!isHome</div>
                        <div class="box-label" style="font-size:8px">HOME</div>

                        <div class="box">$!isPyme</div>
                        <div class="box-label" style="font-size:8px">PYME</div>
                    </td>
                </tr>
                <tr>
                    <td class="col-width-50 line-height textCenter" colspan="2" style="font-size:7px">
                         <b>MEDIO:</b>
                        <div class="box">$!isGeponFibra</div>
                        <div class="box-label">GEPON/FIBRA</div>

                        <div class="box">$!isDslOtros</div>
                        <div class="box-label">DSL/OTROS</div>

                    </td>
                    <td class="col-width-50 line-height textCenter" colspan="3" style="font-size:7px">
                         <b>PLAN:</b>
                        <div class="box">$!isSimetrico</div>
                        <div class="box-label">SIMETRICO</div>

                        <div class="box">$!isAsimetrico</div>
                        <div class="box-label">ASIMETRICO</div>
                    </td>

                </tr><tr>
                    <td class="line-height textCenter labelGris" colspan="2" style="font-size:9px">
                        <b> VELOCIDAD INTERNACIONAL (Mbps) </b>
                    </td>
                    <td class="line-height textCenter labelGris" colspan="2" width="50%" style="font-size:9px">
                        <b> VELOCIDAD LOCAL (Mbps) </b>
                    </td>
                </tr>
                <tr>
                    <td class="line-height textCenter" colspan="1">TASA MAXIMA DE BAJADA</td>
                    <td class="line-height textCenter" colspan="1" width="15%">$!velIntMax Mbps</td>
                    <td class="line-height textCenter" colspan="1">TASA MAXIMA DE BAJADA</td>
                    <td class="line-height textCenter" colspan="1" width="15%">$!velNacMax  Mbps</td>
                </tr>
                <tr>
                    <td class="line-height textCenter" colspan="1">TASA MINIMA DE BAJADA</td>
                    <td class="line-height textCenter" colspan="1" width="15%">$!velIntMin Mbps</td>
                    <td class="line-height textCenter" colspan="1">TASA MINIMA DE BAJADA</td>
                    <td class="line-height textCenter" colspan="1" width="15%">$!velNacMin Mbps</td>
                </tr>
                <tr>
                    <td class="line-height textCenter" colspan="1">TASA MAXIMA DE SUBIDA</td>
                    <td class="line-height textCenter" colspan="1" width="15%">$!velIntMax Mbps</td>
                    <td class="line-height textCenter" colspan="1">TASA MAXIMA DE SUBIDA</td>
                    <td class="line-height textCenter" colspan="1" width="15%">$!velNacMax Mbps</td>
                </tr>
                <tr>
                    <td class="line-height textCenter" colspan="1">TASA MINIMA DE SUBIDA</td>
                    <td class="line-height textCenter" colspan="1" width="15%">$!velIntMin Mbps</td>
                    <td class="line-height textCenter" colspan="1">TASA MINIMA DE SUBIDA</td>
                    <td class="line-height textCenter" colspan="1" width="15%">$!velNacMin Mbps</td>
                </tr>
            </table>
            <div class="labelBlock textCenter" style="margin: 0; border:1px black solid;">PRODUCTOS/SERVICIOS ADICIONALES</div>
            <table class="box-section-content col-width-100 borderTable" style="border-collapse:collapse;border-spacing:0;">
                <tr>
                    <td class="col-width-40 line-height textLeft labelGris" rowspan="3">
                        <b>NETLIFE DEFENSE</b>
                    </td>
                    <td class="col-width-10 line-height textCenter "></td>
                    <td class="col-width-40 line-height textLeft labelGris" rowspan="3" colspan="2">
                        <b>SMART WIFI</b>
                    </td>
                    <td class="col-width-10 line-height textCenter "></td>
                </tr>
                <tr>
                    <td class="col-width-10 line-height textCenter "></td>
                    <td class="col-width-10 line-height textCenter "></td>
                </tr>
                <tr>
                    <td class="col-width-10 line-height textCenter "></td>
                    <td class="col-width-10 line-height textCenter "></td>
                </tr>
                <tr>
                    <td class="col-width-10 line-height textCenter " rowspan="2" colspan="3">Acepto los beneficios de promociones vinculados con la cl&aacute;usula 11 de tiempo m&iacute;nimo de permanencia.</td>
                    <td class="col-width-10 line-height textCenter ">Si</td>
                    <td class="col-width-10 line-height textCenter "><div class="box">$!isAceptacionBeneficios</div></td>
                </tr>
                <tr>
                <td class="col-width-10 line-height textCenter ">No</td>
                    <td class="col-width-10 line-height textCenter "></td>
                </tr>
            </table>
        </div><div style="width:63%; float:right; vertical-align:top;">
            <div class="labelBlock textCenter" style="margin: 0; border:1px black solid;">SERVICIOS Y TARIFAS</div>
            <table class="box-section-content col-width-100 borderTable" style="border-collapse:collapse; border-spacing:0; ">
                <tr>
                    <td class="line-height textCenter labelGris" style="width: 39%"><b>SERVICIO</b></td>
                    <td class="line-height textCenter labelGris" style="width: 10%"><b>CANTIDAD</b></td>
                    <td class="line-height textCenter labelGris" style="width: 13%"><b>INSTALACION</b></td>
                    <td class="line-height textCenter labelGris" style="width: 11%"><b>VALOR MES</b></td>
                    <td class="line-height textCenter labelGris" style="width: 14%"><b>VALOR TOTAL</b></td>
                    <td class="line-height textCenter labelGris" style="width: 13%"><b>OBSERVACIONES</b></td>
                </tr>
                <tr>
                    <td class="line-height labelGris">ACCESO Y NAVEGACI&Oacute;N DE INTERNET</td>
                    <td class="line-height textCenter">$!productoInternetCantidad</td>
                    <td class="line-height textCenter">$!productoInternetInstalacion</td>
                    <td class="line-height textCenter">$!productoInternetPrecio</td>
                    <td class="line-height textCenter">$!productoInternetPrecio</td>
                    <td class="line-height textCenter">$!productoInternetObservaciones</td>
                </tr>
                <tr>
                    <td class="line-height labelGris">IP FIJA PRO</td>
                    <td class="line-height textCenter">$!productoIpFijaCantidad</td>
                    <td class="line-height textCenter">$!productoIpFijaInstalacion</td>
                    <td class="line-height textCenter">$!productoIpFijaPrecio</td>
                    <td class="line-height textCenter">$!productoIpFijaPrecio</td>
                    <td class="line-height textCenter">$!productoIpFijaObservaciones</td>
                </tr>
                <tr>
                    <td class="line-height labelGris">IP ADICIONAL PYME</td>
                    <td class="line-height textCenter">$!productoIpAdicionalCantidad</td>
                    <td class="line-height textCenter">$!productoIpAdicionalInstalacion</td>
                    <td class="line-height textCenter">$!productoIpAdicionalPrecio</td>
                    <td class="line-height textCenter">$!productoIpAdicionalPrecio</td>
                    <td class="line-height textCenter">$!productoIpAdicionalObservaciones</td>
                </tr>
                <tr>
                    <td class="line-height labelGris">INTERNET MOVIL</td>
                    <td class="line-height textCenter">$!productoWifiCantidad</td>
                    <td class="line-height textCenter">$!productoWifiInstalacion</td>
                    <td class="line-height textCenter">$!productoWifiPrecio</td>
                    <td class="line-height textCenter">$!productoWifiPrecio</td>
                    <td class="line-height textCenter">$!productoWifiObservaciones</td>
                </tr>
                <tr>
                    <td class="line-height labelGris">INTERNET PROTEGIDO</td>
                    <td class="line-height textCenter"></td>
                    <td class="line-height textCenter"></td>
                    <td class="line-height textCenter"></td>
                    <td class="line-height textCenter"></td>
                    <td class="line-height textCenter"></td>
                </tr><tr>
                    <td class="line-height labelGris">SEGURO DE EQUIPO</td>
                    <td class="line-height textCenter"></td>
                    <td class="line-height textCenter"></td>
                    <td class="line-height textCenter"></td>
                    <td class="line-height textCenter"></td>
                    <td class="line-height textCenter"></td>
                </tr>              <tr>
                    <td class="line-height labelGris">FIRMA DIGITAL</td>
                    <td class="line-height textCenter"></td>
                    <td class="line-height textCenter"></td>
                    <td class="line-height textCenter"></td>
                    <td class="line-height textCenter"></td>
                    <td class="line-height textCenter"></td>
                </tr>
                <tr>
                    <td class="line-height labelGris">OTROS</td>
                    <td class="line-height textCenter">$!productoOtrosCantidad</td>
                    <td class="line-height textCenter">$!productoOtrosInstalacion</td>
                    <td class="line-height textCenter">$!productoOtrosPrecio</td>
                    <td class="line-height textCenter">$!productoOtrosPrecio</td>
                    <td class="line-height textCenter">$!productoOtrosObservaciones</td>
                </tr>
                <tr>
                    <td class="line-height labelGris">Gastos Administrativos</td>
                    <td class="line-height textCenter"></td>
                    <td class="line-height textCenter"></td>
                    <td class="line-height textCenter"></td>
                    <td class="line-height textCenter"></td>
                    <td class="line-height textCenter"></td>
                </tr>
                <tr>
                    <td class="line-height textCenter" colspan="2" style="border-bottom:1px white solid;">SUBTOTAL:</td>
                    <td class="line-height textCenter">$!subtotalInstalacion</td>
                    <td class="line-height textCenter">SUBTOTAL:</td>
                    <td class="line-height textCenter">$!subtotal</td>
                    <td class="line-height textCenter" style="border-bottom:1px white solid;"></td>
                </tr>
                <tr>
                    <td class="line-height textCenter" colspan="2" style="border-bottom:1px white solid;">IMPUESTOS:</td>
                    <td class="line-height textCenter">$!impInstalacion</td>
                    <td class="line-height textCenter">IMPUESTOS:</td>
                    <td class="line-height textCenter">$!impuestos</td>
                    <td class="line-height textCenter" style="border-bottom:1px white solid;"></td>
                </tr>
                <tr>
                    <td class="line-height textCenter" colspan="2">TOTAL:</td>
                    <td class="line-height textCenter">$!totalInstalacion</td>
                    <td class="line-height textCenter">TOTAL:</td>
                    <td class="line-height textCenter">$total</td>
                    <td class="line-height textCenter"></td>
                </tr>
                <tr>
                    <td colspan="3">

                        <div id="row">
                        <div id="colCell" class="col-width-10 textRight"><b>Promoci&oacute;n:</b></div>
                        <div>$!descInstalacion</div>
                        <div id="colCell" class="col-width-30 textRight">Descuento instalaci&oacute;n:</div>
                        <div class="box">$!isDescInstalacion</div>
                        </div>

                    </td>
                    <td class="line-height textCenter" colspan="3">
                        Mensualidad promo:
                        <div class="box">$!isPrecioPromo</div>
                        $+IVA #meses
                        <div class="box">$!numeroMesesPromo</div>
                    </td>
                </tr>
            </table>
        </div><!-- ========================================== -->
        <!-- Observaciones de los Servicios Contratados -->
        <!-- ========================================== -->        <div style="clear: both;"></div>
        <div id="contenedor" class="col-width-100">
            <div id="row">
                <div id="col" class="col-width-10"> <b>Obs: </b> </div>
                <div id="col" class="col-width-90 labelGris">
                    <span class="textPadding" style="font-size:8px">
                    $!obsServicio ;Dec. Inst.$!descInstalacion ;Desc. Fact. Mensual $!descPlan % ; #Meses Desc. $!mesesDesc ;Aplica Condiciones
                    </span>
                </div>
            </div>
        </div>
        <!-- ========================================== -->
        <!-- Requerimientos Adicionales -->
        <!-- ========================================== -->
        <div style="clear: both;"><br/><br/><br/></div>
        <div class="col-width-100" style="text-align: justify;">
            <div> <b>REQUERIMIENTOS ADICIONALES: </b> </div>
            <br/>
            <div>
                <span>
                    Los siguientes requerimientos podr&aacute;n ser brindados por un valor adicional:
                    <br/>
                </span>
            </div>
            <div class="clausulas" >
                <ul>
                    <li>Obras civiles o cambios de acometida,sujeto a factibilidad.</li>
                    <li>Despu&eacute;s de 300metros de cableado de fibra &oacute;ptica de &uacute;ltima milla, cada metro adicional de cableado de fibra &oacute;ptica tendr&aacute; un valor adicional de $1,00+ impuestos de ley. Sujeto a factibilidad.</li>
                    <li>Nuevas contrataciones, cambios de plan, reactivaciones, cesi&oacute;n de derechos, traslado f&iacute;sico del servicio a otro domicilio o reubicaci&oacute;n en el mismo domicilio,sujeto a factibilidad.</li>
                    <li>Nuevas contrataciones podr&aacute;n ser solicitadas mediante correo con firma digital a info@netlife.ec,llamando al 3920000 &oacute; al 1-700 NETLIFE (638-543), donde la llamada ser&aacute; grabada o visit&aacute;ndonos a nuestros centros de atenci&oacute;n al cliente, cuyos horarios de atenci&oacute;n se encuentran en: http://www.netlife.ec/puntos-de-atencion/ </li>
                    <li>Asistencia t&eacute;cnica a domicilio por solicitud del cliente y debido a causas no imputables a MEGADATOS S.A. en la provisi&oacute;n del servicio de Internet.</li>
                </ul>
            </div>
            <div> <b>CONDICIONES ADICIONALES: </b> <br/> </div>
            <br/>
            <div class="clausulas" >
                <ul>
                    <li>El tiempo de instalaci&oacute;n promedio del servicio es de 7 d&iacute;as h&aacute;biles, sin embargo, puede variar. El servicio esta sujeto a factibilidad, disponibilidad t&eacute;cnica y cobertura de red. No incluye obras civiles o cambios de acometida. El contrato entrar&aacute; en vigencia una vez instalado el servicio y la fecha de activaci&oacute;n del mismo estar&aacute; especificada en la factura correspondiente. El cliente acepta y se obliga a estar presente o delegar a un adulto capaz para recibir el servicio el momento de la instalaci&oacute;n. MEGADATOS no se hace responsable por p&eacute;rdidas o da&ntilde;os que puedan derivarse de la falta de cliente o un adulto responsable de recibir el servicio.</li>
                    <li>La instalaci&oacute;n del servicio incluye un punto de acometida donde se colocar&aacute; el CPE y Router WiFi que ser&aacute;n administrados exclusivamente por MEGADATOS. No se podr&aacute;n retirar,desinstalar o sustituir los equipos proporcionados por MEGADATOS o modificar la configuraci&oacute;n de los mismos. De ninguna manera se podr&aacute; revender, repartir o compartir el servicio a trav&eacute;s de cualquier mecanismo f&iacute;sico o inal&aacute;mbrico o a trav&eacute;s de la compartici&oacute;n de claves de acceso a terceros, no se podr&aacute; instalar servidores con ning&uacute;n tipo de aplicativos, ni c&aacute;maras de video para video vigilancia o para video streaming para fines comerciales. Para disponer de estos servicios el cliente deber&aacute; contratar el plan que contemple aquello, el incumplimiento de estas condiciones ser&aacute; causal de terminaci&oacute;n de contrato en forma inmediata, bastando la notificaci&oacute;n del incumplimiento con la informaci&oacute;n de monitoreo respectivo, sin eximir de la cancelaci&oacute;n de las deudas pendientes, devoluci&oacute;n de equipos y valores de reliquidaci&oacute;n por plazo de permanencia m&iacute;nima.</li>
                    <li>La instalaci&oacute;n del servicio incluye la configuraci&oacute;n para dejar navegando en internet 1 dispositivo. No incluye cableado interno.</li>
                    <li>El cliente es responsable de la instalaci&oacute;n y configuraci&oacute;n interna de su red de &aacute;rea local, as&iacute; como del control de la informaci&oacute;n y navegaci&oacute;n que realice por internet MEGADATOS pone a disposici&oacute;n de los clientes un servicio integral de seguridad inform&aacute;tica para reducir el potencial acceso a informaci&oacute;n que pueda herir la susceptibilidad o que pueda ser fuente de amenazas ciberne&eacute;ticas. Este servicio puede ser activado por el cliente por un precio adicional seg&uacute;n se indique en los planes de la p&aacute;gina web de MEGADATOS y es responsable de su instalaci&oacute;n en sus equipos terminales.</li>
                    <li>El cliente entiende que s&oacute;lo podr&aacute; requerir IPs p&uacute;blicas est&aacute;ticas en planes PYME, sin embargo acepta que la direcci&oacute;n IP asignada podr&iacute;a modificarse por traslados, cambios de plan o mejoras tecnol&oacute;gicas, motivos en los cu&aacute;les existir&aacute; una coordinaci&oacute;n previa para generar el menor impacto posible.</li>
                    <li>El servicio HOME s&oacute;lo es para el segmento residencial, el servicio PYME para profesionales Home/Office que cuenten con m&aacute;ximo 5 equipos y para empresas (no disponibles para Cybers y/o ISPs). El incumplimiento de estas condiciones se convierte en causal de terminaci&oacute;n unilateral de contrato.</li>
                    <li>El cliente acepta que MEGADATOS en planes de Internet, para evitar el SPAM, mantenga restringido el puerto 25 (salvo PYME) y para proteger su servicio de posibles ataques y preservar la seguridad de la red restrinja puertos normalmente usados para este fin como son: 135, 137, 138, 139, 445, 593, 1434, 1900, 5000.</li>
                    <li>Los planes de NETLIFE no incluyen cuentas de correo electr&oacute;nico. En caso de que el cliente lo solicite es posible agregar una cuenta de correo electr&oacute;nico con dominio netlife.ec por un valor adicional. Esta cuenta de correo no incluye el almacenamiento del mismo, sino que es el cliente quien deber&aacute; almacenar los correos que lleguen a su cuenta. MEGADATOS no se responsabiliza de ninguna forma por la perdida de almacenamiento de ning&uacute;n contenido o informaci&oacute;n.</li>
                    <li>El equipo WiFi provisto tiene puertos al&aacute;mbricos que permiten la utilizaci&oacute;n &oacute;ptima de la velocidad ofertada en el plan contratado, adem&aacute;s cuenta con conexi&oacute;n WiFi en planes HOME y PYME, a una frecuencia de 2.4Ghz que permite una velocidad m&aacute;xima de 30Mbps a una distancia de 3mts y pueden conectarse equipos a una distancia de hasta 15metros en condiciones normales, sin embargo, la distancia de cobertura var&iacute;a seg&uacute;n la cantidad de paredes, obst&aacute;culos e interferencia que se encuentren en el entorno. La cantidad m&aacute;xima de dispositivos simultaneos que soporta el equipo WiFi son de 15. El cliente conoce y acepta esta especificaci&oacute;n t&eacute;cnica y que la tecnolog&iacute;a WiFi pierde potencia a mayor distancia y por lo tanto se reducir&aacute; la velocidad efectiva a una mayor distancia de conexi&oacute;n del equipo.</li>
                    <li>Los equipos terminales y cualquier equipo adicional que eventualmente se instalen (CPE) son propiedad de MEGADATOS. En el caso de da&ntilde;o por negligencia del Cliente, &eacute;ste asumir&aacute; el valor total de su reposici&oacute;n considerando el deterioro normal y depreciaci&oacute;n del mismo. Para el caso de servicios FTTH son equipos ONT y WIFI, en el caso de brindar servicios DSL s&oacute;lo ser&aacute; el WIFI y en otros medios s&oacute;lo ser&aacute; el CPE que tendr&aacute; el mismo costo del ONT. El costo es de USD$85 (mas IVA) del ONT, USD$40 (mas IVA) para el equipo WiFi 2.4Ghz, USD$175 (mas IVA) para el ONT+WiFi Dual Band y USD$75 (mas IVA) para el equipo AP Extender WiFi Dual Band, los cu&aacute;les deben incluir sus respectivas fuentes. En caso de p&eacute;rdida de las fuentes, tienen un costo de USD$10,00 cada una. </li>
                    <li>Disponibilidad del servicio 98%. El tiempo promedio de reparaci&oacute;n mensual de todos los clientes de NETLIFE es de 24 horas de acuerdo a la normativa vigente, e inicia despu&eacute;s de haberlo registrado con un ticket en los canales de atenci&oacute;n al cliente de NETLIFE, se excluye el tiempo imputable al cliente.</li>
                    <li>En caso de reclamos o quejas, el tiempo m&aacute;ximo de respuesta es de 7 d&iacute;as despu&eacute;s de haberlas registrado con un ticket en los canales de atenci&oacute;n de NETLIFE.</li>
                    <li>Los canales de atenci&oacute;n al cliente de NETLIFE son: 1) Call Center 2) Centros de Atenci&oacute;n al cliente de NETLIFE 3) P&aacute;gina web. 4) Redes sociales. La informaci&oacute;n de estos canales se encuentra actualizada en la p&aacute;gina web de NETLIFE www.netlife.ec</li>
                    <li>De acuerdo con la norma de calidad para la prestaci&oacute;n de servicios de internet, para reclamos de velocidad de acceso el cliente deber&aacute; realizar los siguientes pruebas: 1) Realizar 2 o 3 pruebas de velocidad en canal vacio, en el veloc&iacute;metro provisto por NETLIFE y guardarlas en un archivo gr&aacute;fico. 2) Contactarse con el call center de NETLIFE para abrir un ticket y enviar los resultados de las pruebas.</li>
                    <li>La atenci&oacute;n telef&oacute;nica del Call Center es 7 d&iacute;as,24 horas incluyendo fines de semana y feriados. El soporte presencial es en d&iacute;as y horas laborables.</li>
                    <li>Cualquier cambio referente a la informaci&oacute;n de la factura o el servicio deber&aacute; notificarse 15 d&iacute;as antes de la finalizaci&oacute;n del ciclo de facturaci&oacute;n.</li>
                    <li>MEGADATOS facturar&aacute; y cobrar&aacute; al ABONADO el servicio contratado en forma mensual basado en el ciclo de facturaci&oacute;n en que haya sido definido.  Para ejecutar cancelaciones de servicio o downgrades, el ABONADO deber&aacute; notificar con 15 d&iacute;as de anticipaci&oacute;n a la fecha de finalizaci&oacute;n de su ciclo de facturaci&oacute;n.</li>
                    <li>El cliente acepta el pago del valor de $1,00 por los reprocesos y cargos bancarios que se produzcan por falta de fondos de acuerdo a las fechas y condiciones de pago del presente contrato, valor que ser&aacute; detallado en la factura del mes correspondiente. En caso de suspensi&oacute;n del servicio por falta de pago deber&aacute; realizar el pago del servicio en uno de los canales de pago correspondientes y comunicarlos a nuestros canales de atenci&oacute;n al cliente. Adicionalmente el cliente acepta el pago de $3,00 por concepto de reconexi&oacute;n que ser&aacute; registrado en la siguiente factura. El tiempo m&aacute;ximo de reconexi&oacute;n del servicio despu&eacute;s del pago es de 24 horas.</li>
                    <li>El cliente acepta que la recepci&oacute;n las facturas mediante la modalidad de facturaci&oacute;n electr&oacute;nica sin costo, o v&iacute;a f&iacute;sica acerc&aacute;ndose a un centro de atenci&oacute;n de MEGADATOS para solicitar su factura previo el pago de $1,00 por ocasi&oacute;n por gastos de procesamiento y emisi&oacute;n de factura. </li>
                    <li>TIPO DE FACTURACI&Oacute;N:  <div class="box">$!isFactElectronica</div>Electr&oacute;nica <div class="box"></div>F&iacute;sica  </li>
                    <li>En caso de tener reclamos debidamente reportados con un ticket y no resueltos por la operadora, puede comunicarse al ARCOTEL a trav&eacute;s del 1-800-567567 o cir@arcotel.gob.ec</li>
                    <li>Para el pago de los servicios de internet, a nombre de usuarios con discapacidad o de la persona natural o jurdica sin fines de lucro que represente legalmente a la persona con discapacidad, se aplica las rebajas establecidas en la Ley Org&aacute;nica de Discapacidades vigente y sus futuras reformas, cumpliendo adicionalmente con la resoluci&oacute;n TEL-072-04-CONATEL-2013 y sus futuras reformas.</li>
                </ul>
            </div><br/><br/><br/>
            <div> <b>CONDICIONES DE OPERACI&Oacute;N: </b> <br/> </div>
            <br/>
            <div class="clausulas" >
                <ul>
                    <li>El cliente es responsable de mantener una energ&iacute;a el&eacute;ctrica regulada de 110V</li>
                    <li>El cliente debe contar con un computador o un dispositivo funcionando adecuadamente con las siguientes caracter&iacute;sticas m&iacute;nimas: Procesador pentium III o superior / 512MB de memoria RAM / 20GB m&iacute;nimo en disco duro / tarjeta de red.</li>
                    <li>Temperatura de operaci&oacute;n normal de los equipos propiedad de MEGADATOS: 0-30 grados cent&iacute;grados.</li>
                    <li>Para tener conocimiento sobre las caracter&iacute;sticas de seguridad que est&aacute;n impl&iacute;citas al intercambiar informaci&oacute;n o utilizar aplicaciones disponibles en la red,favor visite nuestro sitio web: www.netlife.ec</li>
                    <li>Para tener conocimiento de los derechos que lo asisten como usuario, puede encontrar la norma de Calidad de Servicios de Valor Agregado, as&iacute; como el link directo a la p&aacute;gina del ARCOTEL en nuestro sitio web: www.netlife.ec</li>
                    <li>Para realizar la medici&oacute;n del ancho de banda contratado se puede ingresar a la p&aacute;gina web de NETLIFE www.netlife.ec y utilizar el veloc&iacute;metro all&iacute; provisto.</li>
                    <li>El cliente garantizar&aacute; que el personal designado por MEGADATOS pueda ingresar a los sitios donde se encuentren instalados los equipos parte del presente servicio para realizar trabajos de instalaci&oacute;n, mantenimiento correctivo o preventivo, revisi&oacute;n f&iacute;sica del estado de los equipos propiedad de MEGADATOS y cuando MEGADATOS lo requiera. El incumplimiento de estas condiciones ser&aacute; causal de terminaci&oacute;n unilateral de contrato.</li>
                </ul>
            </div>
        </div>
        <br/>');
        
    DBMS_LOB.APPEND(plantilla_editada_contrato,'
        <!-- ================================================= -->
        <!--    Contrato de prestaci&oacute;n de servicios     -->
        <!-- ================================================= -->
        <div style="text-align: justify;">
           <span>
                TERCERA.- OBLIGACIONES DEL ABONADO: Las obligaciones del ABONADO son las siguientes: 3.1.- Cancelar a MEGADATOS los valores correspondientes a los servicios contratados en el plan elegido que consta en el anverso de este Contrato o bajo cualquiera de las modalidades aceptadas por la ley de comercio electr&oacute;nico y en la norma t&eacute;cnica que regula las condiciones generales de los contratos de adhesi&oacute;n. 3.2 Obtener la debida autorizaci&oacute;n y/o licencia del propietario de programas o informaci&oacute;n en caso de que su transferencia a trav&eacute;s de las redes nacionales e internacionales de Internet, as&iacute; lo requieran. 3.3.- Obtener y salvaguardar el uso de la clave de acceso cuando la misma se requiera para la transferencia de informaci&oacute;n a trav&eacute;s de las redes nacionales e internacionales de Internet, 3.4.- Respetar y someterse en todo a la Ley Org&aacute;nica de Telecomunicaciones, Ley de Propiedad Intelectual, y en general a todas las leyes que regulan la materia en el Ecuador. 3.5.- Informarse adecuadamente de las condiciones de cada uno de los servicios que brinda MEGADATOS,los cuales se rigen por el presente Contrato y las leyes aplicables vigentes,no pudiendo alegar desconocimiento de dichas condiciones contractuales. 3.6.- Mantener actualizada la informaci&oacute;n de contacto,correo,tel&eacute;fono fijo,tel&eacute;fono m&oacute;vil con MEGADATOS para garantizar la recepci&oacute;n de la informaci&oacute;n que genera la relaci&oacute;n contractual.
                </br>
                CUARTA.- OBLIGACIONES DE MEGADATOS: Las obligaciones de MEGADATOS son las siguientes: 4.1.- Suministrar al ABONADO el servicio de acceso a las redes nacionales e internacionales de Internet acatando las disposiciones previstas en la Ley y en el presente Contrato. 4.2.- Actuar con la debida diligencia en la prestaci&oacute;n del servicio, 4.3.- Respetar y someterse en todo a la Ley Org&aacute;nica de Telecomunicaciones, Ley Org&aacute;nica de Defensa del Consumidor,y en general a todas las leyes que en el Ecuador regulan la materia. 4.4.- Implementar los mecanismos necesarios que permitan precautelar la seguridad de sus redes. 4.5.- Entrega o prestar oportuna y eficientemente el servicio,de conformidad a las condiciones establecidas en el contrato y normativa aplicable,sin ninguna variaci&oacute;n. 4.6.- Notificar cualquier modificaci&oacute;n de los planes tarifarios al ARCOTEL con al menos 48 horas a su fecha de vigencia seg&uacute;n lo establecido en la Ley Org&aacute;nica de Telecomunicaciones.
                <br/>QUINTA.- ALCANCE DE LA RESPONSABILIDAD DE MEGADATOS: Es responsabilidad de MEGADATOS cumplir con las obligaciones contempladas en el presente Contrato. Sin perjuicio de lo anterior se deja expresa constancia que MEGADATOS no se har&aacute; responsable en los siguientes casos: 5.1.- En caso de que por razones de cambio de tarifas,reformas legales,caso fortuito o fuerza mayor se vea en la obligaci&oacute;n de suspender el servicio. No obstante lo anterior,MEGADATOS se compromete a informar inmediatamente de este hecho al ABONADO. 5.2.- En caso de que se presente transmisi&oacute;n de virus a trav&eacute;s de las redes. 5.3.- El ABONADO recibir&aacute; los servicios contratados de forma continua, regular, eficiente, con calidad y eficacia, salvo que sea detectado su mal uso, su falta de pago del ABONADO, (aplicar&aacute; al d&iacute;a siguiente de cumplida la fecha m&aacute;xima de pago), por caso fortuito, por uso indebido de los servicios contratados o uso ilegal y en form diferente al paquete contratado, comercializaci&oacute;n, subarrendamiento, por mandato judicial y por las dem&aacute;s causas previstas en el ordenamiento jur&iacute;dico vigente lo cual provocar&aacute; que MEGADATOS suspenda sus servicios. 5.4.- Por da&ntilde;os que  llegaran a producirse en los equipos como consecuencia de la utilizaci&oacute;n de los equipos o del servicio contratado sin contemplar las condiciones de operaci&oacute;n. 5.5.- En caso de Incumplimiento por parte del ABONADO,de las condiciones contractuales y sus obligaciones establecidas en la Ley Org&aacute;nica de Defensa del Consumidor y otras leyes aplicables vigentes. EL ABONADO declara que acepta desde ya todas y cada una de las modificaciones que MEGADATOS se vea obligado a efectuar a las condiciones pactadas en el presente Contrato que se deriven de reformas a la normativa al momento de suscripci&oacute;n del mismo. Tales modificaciones no se entender&aacute;n como terminaci&oacute;n anticipada del contrato ni generar&aacute;n responsabilidad alguna para MEGADATOS. 5.6.- MEGADATOS no podr&aacute; bloquear,priorizar,restringir o discriminar de modo arbitrario y unilateral aplicaciones,contenidos o servicios sin consentimiento del ABONADO o por orden expresa de la autoridad competente. Del mismo modo podr&aacute; ofrecer,si el ABONADO lo solicita,servicio de control y bloqueo de contenidos que atenten contra la ley,la moral o las buenas costrumbres,para lo cual informar&aacute; oportunamente al ABONADO cual es el alcance de la tarifa o precio y modo de funcionamiento de estos. 5.7.- Las condiciones de la prestaci&oacute;n de los servicios contratados se sujetar&aacute;n a las leyes,reglamentos,resoluciones,regulaciones,decretos y toda decisi&oacute;n de car&aacute;cter general de cualquier instituci&oacute;n del Estado existente o que se dictaren durante el plazo de ejecuci&oacute;n del t&iacute;tulo habilitante que no se encuentren especificadas en la Legislaci&oacute;n Aplicable.
                <br/>
                SEXTA.- DERECHOS DEL ABONADO: 6.1 Recibir el servicio de acceso a las redes nacionales e internacionales de Internet seg&uacute;n las disposiciones previstas en la ley y en el presente contrato. 6.2.- Solicitar soporte t&eacute;cnico seg&uacute;n las condiciones establecidas en la ley y el presente contrato en caso de ser requerido. 6.3.- Recibir todos los derechos adquiridos seg&uacute;n la ley org&aacute;nica de las telecomunicaciones,el reglamento general,el reglamento de prestaci&oacute;n de servicios de valor agregado y la Ley de defensa del consumidor. 6.4.- Recibir compensaciones por parte del proveedor seg&uacute;n lo dispuesto por el organismo de control,como notas de cr&eacute;dito en todos los casos por el servicio no provisto seg&uacute;n las condiciones contractuales. 6.5.- Los nuevos derechos y beneficios para el ABONADO que se establezcan a futuro se incorporar&aacute;n de manera autom&aacute;tica al presente contrato por disposici&oacute;n del ARCOTEL.
                <br/>SEPTIMA.- PRECIO Y FORMA DE PAGO: El precio de los servicios contratados por EL ABONADO y los impuestos constan descritos en el anverso de este Contrato, el cual puede ser cancelado en dinero en efectivo, dep&oacute;sito, transferencia mediante bot&oacute;n de pago, d&eacute;bito, tarjeta de cr&eacute;dito u otras que implemente o facilite MEGADATOS, de acuerdo a los t&eacute;rminos de contrataci&oacute;n. En caso de que EL ABONADO incurra en mora de uno o m&aacute;s pagos,MEGADATOS se reserva el derecho de suspender el servicio y dar por terminado el mismo sin notificaci&oacute;n o requerimiento alguno; sin perjuicio de las acciones legales que el incumplimiento de esta obligaci&oacute;n diera lugar. En caso de mora MEGADATOS aplicar&aacute; la m&aacute;xima tasa de inter&eacute;s permitida por la ley por el periodo en mora. Para el caso de que se contrate servicios adicionales y suplementarios con costo,el ABONADO se compromete a firmar una adenda verbal grabada,electr&oacute;nica con firma digital o f&iacute;sica al presente contrato,de igual manera cuando se desuscriba de los mismos.
                <br/>
                OCTAVA.- PRIVACIDAD Y TRATAMIENTO DE INFORMACI&Oacute;N: MEGADATOS garantizar&aacute; la privacidad y confidencialidad de la informaci&oacute;n del ABONADO y s&oacute;lo la utilizar&aacute; para brindar el servicio contratado por el ABONADO,por lo que el ABONADO conoce y
                <div id="col" class="col-width-10">
                    <div class="box">$!isSiAutoriza</div>
                    <div class="box-label">Si</div>
                </div>
                <div id="col" class="col-width-10">
                    <div class="box">$!isNoAutoriza</div>
                    <div class="box-label">No</div>
                </div>
                 autoriza que MEGADATOS pueda proporcionar a terceros datos necesarios para poder realizar la entrega de estado de cuenta,facturaci&oacute;n,recordatorios de fechas de pago o montos de pago,fidelizaci&oacute;n,informaci&oacute;n de nuevos servicios,informaci&oacute;n de promociones especiales,entre otros; as&iacute; mismo tambi&eacute;n autoriza a hacer uso de esta informaci&oacute;n para fines comerciales o de brindar beneficios al ABONADO a trav&eacute;s de alianzas desarrolladas. Adicionalmente EL ABONADO acepta expresamente que MEGADATOS puede utilizar medios electr&oacute;nicos y llamadas para: 8.1.- Notificar cambios relacionados con los t&eacute;rminos y condiciones del presente CONTRATO, 8.2.- Realizar gestiones de cobtranzas y dem&aacute;s promociones aplicables de acuerdo a la normativa vigente. Sin embargo de lo anterior,MEGADATOS podr&aacute; entregar los datos del ABONADO en caso de requerimientos realizados por autoridad competente conforme al ordenamiento jur&iacute;dico vigente y particularmente de la Agencia de Regulaci&oacute;n y Control de las Telecomunciaciones para el cumplimiento de sus funciones.
                <br/>
                NOVENA.- FACTURACI&Oacute;N: MEGADATOS facturar&aacute; y cobrar&aacute; al ABONADO el servicio contratado en forma mensual basado en el ciclo de facturaci&oacute;n en que haya sido definido.  Para ejecutar cancelaciones de servicio o downgrades,el ABONADO deber&aacute; notificar con 15 d&iacute;as de anticipaci&oacute;n a la fecha de finalizaci&oacute;n de su ciclo de facturacion. El primer pago constar&aacute; del valor de instalaci&oacute;n y el valor proporcional del primer per&iacute;odo de consumo correspondiente. MEGADATOS entregar&aacute; a sus ABONADOs las facturas de conformidad con la ley,sin embargo la no recepci&oacute;n de dicho documento no exime al ABONADO del pago correspondiente. El ABONADO cancelar&aacute; por periodos mensuales a MEGADATOS por la prestaci&oacute;n del servicio contratado a los precios pactados a trav&eacute;s de &eacute;ste instrumento y sus anexo(servicios adicionales),hasta el fin del per&iacute;odo; si el ABONADO no cancelare los valores facturados dentro del plazo previsto,MEGADATOS suspender&aacute; de forma autom&aacute;tica los servicios en cualquier momento a partir del vencimiento de dicho plazo. El ABONADO podr&aacute; pedir la reactivaci&oacute;n del servicio en un m&aacute;ximo de 30 d&iacute;as posteriores a la suspensi&oacute;n,previo al pago de los valores adeudados,caso contrario el servicio ser&aacute; dado por cancelado. El tiempo de reactivaci&oacute;n del servicio es de 24 horas despu&eacute;s de que el ABONADO haya pagado los valores pendientes y haya hecho el pedido de reactivaci&oacute;n.
                <br/>DECIMA.- VIGENCIA: El plazo de duraci&oacute;n del presente Contrato es de 36 meses y tendr&aacute; vigencia desde la fecha de instalaci&oacute;n y activaci&oacute;n del servicio que se indicar&aacute; en la facturaci&oacute;n mensual, en el cual
                <div id="col" class="col-width-10">
                    <div class="box">$!isSiRenueva</div>
                    <div class="box-label">Si</div>
                </div>
                <div id="col" class="col-width-10">
                    <div class="box">$!isNoRenueva</div>
                    <div class="box-label">No</div>
                </div>

                se renovar&aacute; autom&aacute;ticamente en per&iacute;odos iguales y sucesivos, mientras las partes no soliciten una terminaci&oacute;n del mismo, se podr&aacute; realizar una revisi&oacute;n periodica de tarifas en funci&oacute;n de condiciones de mercado y de mutuo acuerdo. El operador respetar&aacute; las condiciones establecidas en la ley org&aacute;nica de defensa del consumidor para la prestaci&oacute;n de los servicios entre las partes.
                <br/>
                D&Eacute;CIMO PRIMERA.- TERMINACI&Oacute;N DEL CONTRATO: Para el caso de terminaci&oacute;n del contrato, el ABONADO se compromete a cancelar los valores adeudados a MEGADATOS y a entregar los equipos de propiedad de MEGADATOS en las oficinas de MEGADATOS habilitados para este prop&oacute;sito que se indican en la secci&oacute;n de atenci&oacute;n al cliente de la p&aacute;gina web www.netlife.ec, en perfectas condiciones, salvo por deterioros normales causados por el uso diligente. Sin perjuicio de lo anterior, son causales de terminaci&oacute;n anticipada del presente instrumento,las siguientes: 11.1.- Aplicaci&oacute;n de las normas legales,el caso fortuito o fuerza mayor que obliguen a MEGADATOS a suspender definitivamente el servicio. 11.2.- La suspensi&oacute;n definitiva de servicios prestados por los proveedores de MEGADATOS. 11.3.- Incumplimiento de Ias obligaciones contractuales de las partes,no pago del servicio o mal uso del servicio derivadas del presente Contrato,incluyendo la manipulaci&oacute;n o retiro de equipos provistos por MEGADATOS,y todas las mecionadas en el presente contrato en el literal condiciones. 11.4.- En caso de que el servicio se est&eacute; utilizando en un Cyber o ISP,bastando para la terminaci&oacute;n un informe/reporte generado por MEGADATOS que confirme esto,sin eximir del pago de todos los valores que se adeuden,entregar los equipos que proveen del servicio o su valor en efectivo y cumplir las condiciones de permanencia m&iacute;nima. 11.5.- Por acuerdo mutuo. 11.6.- Por decisi&oacute;n unilateral de acuerdo a la ley de defensa del consumidor,sin que hayan multas o recargos para ello.
                Para el caso puntual de promociones, el ABONADO
                <div id="col" class="col-width-10">
                    <div class="box">$!isSiAcceder</div>
                    <div class="box-label">Si</div>
                </div>
                <div id="col" class="col-width-10">
                    <div class="box">$!isNoAcceder</div>
                    <div class="box-label">No</div>
                </div>
                desea acceder a las promociones que consideran un plazo m&iacute;nimo de permanencia es de 36 meses para hacerlas efectivas y permanecer vigentes y acceder a los promocionales de MEGADATOS, en tal virtud, en caso de una terminaci&oacute;n anticipada del contrato, el ABONADO dejar&aacute; de beneficiarse de dicho descuento, promoci&oacute;n o costo de instalaci&oacute;n,y por lo tanto se le aplicar&aacute;n las tarifas regulares por los servicios e instalaci&oacute;n contratados prorrateados en funci&oacute;n del tiempo de permanencia. Para tal efecto en la &uacute;ltima factura emitida al ABONADO, se reflejar&aacute; la respectiva reliquidaci&oacute;n de valores del servicio contratado en base al valor real del mismo.
                <br/>
                D&Eacute;CIMO SEGUNDA.- DECLARACI&Oacute;N FUNDAMENTAL: El ABONADO declara que ha obtenido de forma oportuna por parte de MEGADATOS,toda la informaci&oacute;n veraz y completa del servicio contratado. As&iacute; mismo declara que conoce &iacute;ntegramente el presente contrato en su anverso y reverso y que lo acepta en todas sus partes por convenir a sus intereses.
                <br/>
                D&eacute;CIMA TERCERA.- CESI&oacute;N: EL ABONADO acepta desde ya cualquier cesi&oacute;n parcial o total que realice MEGADATOS de los derechos y/u obligaciones contenidos en este Contrato. El ABONADO puede ceder el presente contrato previo a realizar el tr&aacute;mite correspondiente de cesi&oacute;n de derechos en los canales de atenci&oacute;n al ABONADO de NETLIFE.
                <br/>D&eacute;CIMO CUARTA.- ACUERDO TOTAL: El presente Contrato Contiene los acuerdos totales de las partes y deja sin efecto cualquier negociaci&oacute;n,entendimiento,contrato o convenio que haya existido previamente entre el ABONADO y MEGADATOS,el presente instrumento incluye todas las condiciones a las que se compromete la empresa y el alcance &uacute;nico de sus servicios y deja sin efecto cualquier informaci&oacute;n adicional recibida que no conste en el mismo. Si el ABONADO desea contratar servicios adicionales, &eacute;stos ser&aacute;n agregados al presente contrato.
                <br/>
                D&Eacute;CIMO QUINTA.- CONTROVERSIAS: Las controversias o diferencias que surjan entre las partes con ocasi&oacute;n de la firma, ejecuci&oacute;n, interpretaci&oacute;n, pr&oacute;rroga o terminaci&oacute;n del Contrato, as&iacute; como de cualquier otro asunto relacionado con el presente Contrato, ser&aacute;n sometidas a la revisi&oacute;n de las partes para buscar un arreglo directo, en t&eacute;rmino no mayor a CINCO(5) d&iacute;as h&aacute;biles a partir de la fecha en que cualquiera de las partes comunique por escrito a la otra parte la existencia de una diferencia y la explique someramente. Si no se resolviere de esta manera, tratar&aacute;n de solucionarlo con la asistencia de un mediador de la C&aacute;mara de Comercio de Quito; en caso de que no pueda ser solucionada en mediaci&oacute;n  las partes
                <div id="col" class="col-width-10">
                    <div class="box">$!isSiMediacion</div>
                    <div class="box-label">Si</div>
                </div>
                <div id="col" class="col-width-10">
                    <div class="box">$!isNoMediacion</div>
                    <div class="box-label">No</div>
                </div>
                , seg&uacute;n sus intereses podr&aacute;n someterse a la Justicia Ordinaria y/o a trav&eacute;s de un Tribunal de Arbitraje de la C&aacute;mara de Comercio de Quito, el mismo que se sujetar&aacute; a lo dispuesto en la Ley de Arbitraje y Mediaci&oacute;n, y dem&aacute;s normativas y preceptos.

                <br/>
                D&Eacute;CIMO SEXTA.- NOTIFICACIONES: Toda y cualquier notificaci&oacute;n que requiera realizarse en relaci&oacute;n con el presente Contrato,se har&aacute; por escrito a las siguientes direcciones: Uno.- MEGADATOS:  Quito: Av. N&uacute;&ntilde;ez de Vela E3-13 y Atahualpa,Edificio torre del Puente Piso 2,Guayaquil: Av. Rodrigo de Chavez,Parque Empresarial Col&oacute;n Ed.Coloncorp,Torre 6,Locales 4 y 5,&oacute; en la direcci&oacute;n de correo electr&oacute;nico info@netlife.ec</span> 14.2.- ABONADO en la direcci&oacute;n indicada en el anverso del presente contrato o en su direcci&oacute;n de correo electr&oacute;nico.
                De presentarse cambios en las direcciones enunciadas,la parte respectiva dar&aacute; aviso escrito de tal hecho a la otra,dentro de las 24 horas de producido el cambio. Para constancia de todo lo expuesto y convenido,las partes suscriben el presente contrato,en la ciudad y fecha indicada en el anverso del presente contrato,en tres ejemplares de igual tasa y valor.
                </div>

        <!-- ========================================== -->
        <!-- Firma del Cliente  -->
        <!-- ========================================== -->
        <br/>
        <div style="clear: both;"></div>
        <div id="contenedor" class="col-width-100">
            <div id="row">
                <div id="colCell" class="col-width-50" style="text-align:center">
                    <div id="contenedor" class="col-width-100">
                        <div id="row" >
                            <div id="colCell" class="col-width-25"></div>
                            <div id="colCell" class="col-width-50" style="height:35px">

                            </div>
                            <div id="colCell" class="col-width-25"></div>
                        </div>
                        <div id="row" >
                            <div id="colCell" class="col-width-25"></div>
                            <div id="colCell" class="col-width-50"><hr></div>
                            <div id="colCell" class="col-width-25"></div>
                        </div>
                        <div id="row">
                            <div id="colCell" class="col-width-25"></div>
                            <div id="colCell" class="col-width-50"><span>MEGADATOS</span><input id="inputfirma1" name="FIRMA_CONT_MD_FINAL_EMPRESA" type="text" value="" style="background-color:#fff; width:0.0em; margin-left:0.75em; border-style: hidden; opacity:0; border:none;" readonly/></div>
                            <div id="colCell" class="col-width-25"></div>
                        </div>
                    </div>
                </div><div id="colCell" class="col-width-50" style="text-align:center">
                    <div id="contenedor" class="col-width-100">
                        <div id="row" >
                            <div id="colCell" class="col-width-25"></div>
                            <div id="colCell" class="col-width-50" style="height:35px">

                            </div>
                            <div id="colCell" class="col-width-25"></div>
                        </div>
                        <div id="row" >
                            <div id="colCell" class="col-width-25"></div>
                            <div id="colCell" class="col-width-50"><hr></div>
                            <div id="colCell" class="col-width-25"></div>
                        </div>
                        <div id="row">
                            <div id="colCell" class="col-width-25"></div>
                            <div id="colCell" class="col-width-50"><span>Firma del Cliente</span><input id="inputfirma2" name="FIRMA_CONT_MD_FINAL_CLIENTE" type="text" value="" style="background-color:#fff; width:0.0em; margin-left:0.75em; border-style: hidden; opacity:0; border:none;" readonly/></div>
                            <div id="colCell" class="col-width-25"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
                    <div id="contenedor" style="float:right">
                        <div id="row">
                            <div id="col" class="col-width-100" style="text-align:center">
                                FO-VEN-01
                            </div>
                        </div>
                        <div id="row">
                            <div id="col" class="col-width-100" style="text-align:center">
                                ver-08 | Feb-2019
                            </div>
                        </div>
                    </div>

		<!-- ============================== -->
        <!--      Servicios Contratados     -->
        <!-- ============================== -->
        <div style="clear: both;"><br/><br/><br/></div>
        <div class="labelBlock">DOCUMENTOS QUE DEBEN ADJUNTARSE</div>
        <br/>
        <div id="contenedor" class="col-width-100" style="text-align: justify;">
            <div id="row">
                <div id="colCell" class="col-width-60">
                    <div> <b>Personas Naturales: </b> </div>
                    <div class="clausulas" >
                        <ul>
                            <li>Copia de C&eacute;dula de Identidad o pasaporte</li>
                            <li>Copia de encabezado de estado de cuenta (Corriente/Ahorro/TC) en caso de hacer d&eacute;bito autom&aacute;tico.</li>
                            <li>Copia de la calificaci&oacute;n de discapacidad emitida por el CONADIS, que determine el tipo y porcentaje de discapacidad igual o mayor al 30%. (Si aplica)</li>
                            <li>En caso de discapacidad, factura original de un servicio b&aacute;sico que demuestre la residencia del solicitante para acceder al servicio.</li>
                        </ul>
                    </div>
                </div>
                <div id="colCell" class="col-width-5"></div>
                <div id="colCell" class="col-width-35">
                    <div> <b>Personas Jur&iacute;dicas: </b> </div>
                    <div class="clausulas" >
                        <ul>
                            <li>Copia del RUC</li>
                            <li>Copia de encabezado de estado de cuenta (Corriente/Ahorro/TC) en caso de hacer d&eacute;bito autom&aacute;tico.</li>
                            <li>Copia de c&eacute;dula o pasaporte de representante legal.</li>
                            <li>Nombramiento de representante legal (inscrito en registro mercantil)</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- ========================================== -->
        <!-- Forma de pago e informacion de credito -->
        <!-- ========================================== -->

        <div style="clear: both;"><br/><br/><br/></div>
        <div class="labelBlock">FORMA DE PAGO E INFORMACI&Oacute;N DE CR&Eacute;DITO</div>
        <div id="contenedor" class="col-width-100">
            <div id="row">
                <div id="col" class="col-width-5"></div>
                <div id="col" class="col-width-25">
                    <div class="box-label">Tarjeta de Cr&eacute;dito</div>
                    <div class="box">$isTarjetaCredito</div>
                </div>
                <div id="col" class="col-width-25">
                    <div class="box-label">Cuenta Corriente</div>
                    <div class="box">$isCuentaCorriente</div>
                </div>
                <div id="col" class="col-width-25">
                    <div class="box-label">Cuenta de Ahorros</div>
                    <div class="box">$isCuentaAhorros</div>
                </div>
                <div id="col" class="col-width-20">
                    <div class="box-label">Efectivo</div>
                    <div class="box">$isEfectivo</div>
                </div>
            </div>
        </div>
        <br/>
        <div style="text-align: justify;">
            <span>El cliente declara haber le&iacute;do este contrato y la solicitud de prestaci&oacute;n de servicios en su totalidad y declara que esta conforme con todas y cada una de sus cl&aacute;usulas. El cliente declara que la informaci&oacute;n suministrada a MEGADATOS es ver&aacute;z y correcta. Adicionalmente autoriza a MEGADATOS a verificarla. El cliente autoriza a MEGADATOS expresamente a entregar y requerir informaci&oacute;n,en forma directa,a los buros de informaci&oacute;n crediticia o entidades designadas para estas calificaciones sobre su comportamiento y capacidad de pago,su desempe&ntilde;o como deudor,para valorar su riesgo futuro.
            </span>
        </div>

        <!-- ========================================== -->
        <!-- Firma del Cliente  -->
        <!-- ========================================== -->
        <div style="clear: both;"><</div>
        <div id="contenedor" class="col-width-100">
            <div id="row">
                <div id="colCell" class="col-width-50" style="text-align:center">
                    <div id="contenedor" class="col-width-100">
                        <div id="row" >
                            <div id="colCell" class="col-width-25"></div>
                            <div id="colCell" class="col-width-50" style="height:80px">

                            </div>
                            <div id="colCell" class="col-width-25"></div>
                        </div>
                        <div id="row" >
                            <div id="colCell" class="col-width-25"></div>
                            <div id="colCell" class="col-width-50"><hr></div>
                            <div id="colCell" class="col-width-25"></div>
                        </div>
                        <div id="row">
                            <div id="colCell" class="col-width-25"></div>
                            <div id="colCell" class="col-width-50"><span>Firma del Cliente</span><input id="inputfirma3" name="FIRMA_CONT_MD_FORMA_PAGO" type="text" value="" style="background-color:#fff; width:0.0em; margin-left:0.75em; border-style: hidden; opacity:0; border:none;" readonly/></div>
                            <div id="colCell" class="col-width-25"></div>
                        </div>
                    </div>
                </div>
                <div id="colCell" class="col-width-50">
                    <div id="contenedor" class="col-width-100">
                        <div id="row" >
                            <div id="col" class="col-width-25">Nombre:</div>
                            <div id="col" class="col-width-50 labelGris">
                                <span class="textPadding">$nombresApellidos</span>
                            </div>
                            <div id="col" class="col-width-25"></div>
                        </div>
                        <div id="row" >
                            <div id="col" class="col-width-25">CI/RUC:</div>
                            <div id="col" class="col-width-50 labelGris">
                                <span class="textPadding">$identificacion</span>
                            </div>
                            <div id="col" class="col-width-25"></div>
                        </div>
                    </div>
                </div>
            </div>


    </body>
</html>');
        
    UPDATE DB_FIRMAELECT.ADM_EMPRESA_PLANTILLA t SET t.HTML = plantilla_editada_contrato WHERE t.COD_PLANTILLA = 'contratoMegadatos' AND t.ESTADO = 'Activo';
    
    DBMS_OUTPUT.PUT_LINE('OK contratoMegadatos ' || DBMS_LOB.GETLENGTH(plantilla_editada_contrato));
    ----------------------
  
    ------- CONTRATO SECURITY DATA ----- 
    SELECT t.* INTO registro_contrato_sd FROM DB_FIRMAELECT.ADM_EMPRESA_PLANTILLA t WHERE t.COD_PLANTILLA = 'contratoSecurityData' AND t.ESTADO = 'Activo';
    
    plantilla_editada_contrato_sd := REPLACE(registro_contrato_sd.HTML, '.ft08{font-size:13px;line-height:21px;font-family:"ARIAL";color:#000000;}', '.ft08{font-size:13px;line-height:21px;font-family:"ARIAL";color:#000000;}
    .box{height: 16px; width: auto; border: 1px solid black; display:inline-block; border-radius: 2px; -moz-border-radius: 2px; -webkit-border-radius: 2px ; vertical-align: center; text-align: left;}');
        
    plantilla_editada_contrato_sd := REPLACE(plantilla_editada_contrato_sd, '<p style="position:absolute;top:3304px;left:134px;white-space:nowrap" class="ft04">', '<p style="position:absolute;top:3318px;left:134px;white-space:nowrap" class="ft04">');
    
    plantilla_editada_contrato_sd := REPLACE(plantilla_editada_contrato_sd, '<p style="position:absolute;top:149px;left:58px;width:793px;height:18px;border: solid 1px #000000;" class="ft06"><i><b>Persona Natural:<input type="checkbox" name="persona" value="$isPersonaNatural" checked=""/></b></i></p>', '<p style="position:absolute;top:149px;left:58px;width:793px;height:18px;border: solid 1px #000000;" class="ft06"><i><b>Persona Natural:</b></i><span class="box">$isPersonaNatural</span></p>');
      
    plantilla_editada_contrato_sd := REPLACE(plantilla_editada_contrato_sd, '<p style="position:absolute;top:169px;left:58px;width:793px;height:24px;border: solid 1px #000000;" class="ft00">Nombres:<input type="text" name="nombre" id="nombre" size="34" value="$nombresApellidos"/>&nbsp;&nbsp;CI:<input type="text" name="cedula" id="cedula" size="22" value="$identificacion"/>&nbsp;&nbsp;RUC:<input type="text" name="ruc" id="ruc" size="17" value="$ruc"/></p>', '<p style="position:absolute;top:169px;left:58px;width:793px;height:24px;border: solid 1px #000000;" class="ft00">Nombres:<span class="box" style="width:340px;">$nombresApellidos</span>&nbsp;&nbsp;CI:<span class="box" style="width:150px;">$identificacion</span>&nbsp;&nbsp;RUC:<span class="box" style="width:150px;">$ruc</span></p>');
      
    plantilla_editada_contrato_sd := REPLACE(plantilla_editada_contrato_sd, '<p style="position:absolute;top:3020px;left:134px;white-space:nowrap" class="ft05">(fecha de emisi&oacute;n):<input type="text" name="fecha" id="fecha" value="$fechaActual"/></p>', '<p style="position:absolute;top:3020px;left:134px;white-space:nowrap" class="ft05">(fecha de emisi&oacute;n):<span class="box" style="width:120px; text-align: center;">$fechaActual</span></p>');
  
    plantilla_editada_contrato_sd := REPLACE(plantilla_editada_contrato_sd, '<p style="position:absolute;top:3292px;left:134px;white-space:nowrap" class="ft04"><b>SECURITY DATA SEGURIDAD EN</b></p>','<p style="position:absolute;top:3303px;left:134px;white-space:nowrap" class="ft04"><span><b>SECURITY DATA SEGURIDAD EN</b></span><input id="inputfirma1" name="FIRMA_CONT_SD_EMPRESA" type="text" value="" style="background-color:#fff; width:0.0em; margin-left:0.75em; border-style: hidden; opacity:0; border:none;" readonly/></p>');
  
    plantilla_editada_contrato_sd := REPLACE(plantilla_editada_contrato_sd, '<p style="position:absolute;top:3304px;left:134px;white-space:nowrap" class="ft04"><b>DATOS Y FIRMA DIGITAL S. A.</b></p>','<p style="position:absolute;top:3316px;left:134px;white-space:nowrap" class="ft04"><b>DATOS Y FIRMA DIGITAL S. A.</b></p>');
     
    plantilla_editada_contrato_sd := REPLACE(plantilla_editada_contrato_sd, '<p style="position:absolute;top:3303px;left:512px;white-space:nowrap" class="ft04"><b>EL SUSCRIPTOR</b></p>','<p style="position:absolute;top:3303px;left:512px;white-space:nowrap" class="ft04"><span><b>EL SUSCRIPTOR</b></span><input id="inputfirma2" name="FIRMA_CONT_SD_CLIENTE" type="text" value="" style="background-color:#fff; width:0.0em; margin-left:0.75em; border-style: hidden; opacity:0; border:none;" readonly/></p>');
   
    UPDATE DB_FIRMAELECT.ADM_EMPRESA_PLANTILLA t SET t.HTML = plantilla_editada_contrato_sd WHERE t.COD_PLANTILLA = 'contratoSecurityData' AND t.ESTADO = 'Activo';
    
    DBMS_OUTPUT.PUT_LINE('OK contratoSecurityData ' || DBMS_LOB.GETLENGTH(plantilla_editada_contrato_sd));
    --------------------
      
  
    ------- FORMULARIO SECURITY DATA ----- 
    SELECT t.* INTO registro_formulario_sd FROM DB_FIRMAELECT.ADM_EMPRESA_PLANTILLA t WHERE t.COD_PLANTILLA = 'formularioSecurityData' AND t.ESTADO = 'Activo';
  
    plantilla_editada_form_sd := REPLACE(registro_formulario_sd.HTML, '.ft05{font-size:10px;line-height:15px;font-family:"ARIAL";color:#000000;}
-->', '.ft05{font-size:10px;line-height:15px;font-family:"ARIAL";color:#000000;}
    .box{ height: 16px; width: auto; border: 1px solid black; display:inline-block; border-radius: 2px; -moz-border-radius: 2px; -webkit-border-radius: 2px; vertical-align: center; text-align: left;}
-->');
     
    plantilla_editada_form_sd := REPLACE(plantilla_editada_form_sd, '<p style="position:absolute;top:235px;left:559px;white-space:nowrap" class="ft01">Fecha:<input type="text" id="fecha" name="fecha" size="32" required value="$fechaActual"></p>', '<p style="position:absolute;top:235px;left:559px;white-space:nowrap" class="ft01">Fecha:<span class="box" style="width:211px">$fechaActual</span></p>');
  
    plantilla_editada_form_sd := REPLACE(plantilla_editada_form_sd, '<p style="position:absolute;top:274px;left:94px;white-space:nowrap" class="ft01">Nombres y Apellidos*:<input type="text" id="nombres" name="nombres" size="86" value="$nombresApellidos" required></p>', '<p style="position:absolute;top:274px;left:94px;white-space:nowrap" class="ft01">Nombres y Apellidos*:<span class="box" style="width:590px">$nombresApellidos</span></p>');
 
    plantilla_editada_form_sd := REPLACE(plantilla_editada_form_sd, '<p style="position:absolute;top:299px;left:94px;white-space:nowrap" class="ft01">No. C&eacute;dula / No. Pasaporte*:<input type="text" id="cedula" name="cedula" size="38" value="$identificacion" required></p>', '<p style="position:absolute;top:299px;left:94px;white-space:nowrap" class="ft01">No. C&eacute;dula / No. Pasaporte*:<span class="box" style="width:250px">$identificacion</span></p>');
  
    plantilla_editada_form_sd := REPLACE(plantilla_editada_form_sd, '<p style="position:absolute;top:299px;left:559px;white-space:nowrap" class="ft01">Nacionalidad*:<input type="text" id="nacionalidad" name="nacionalidad" size="25" value="$nacionalidad" required></p>', '<p style="position:absolute;top:299px;left:559px;white-space:nowrap" class="ft01">Nacionalidad*:<span class="box" style="width:169px">$nacionalidad</span></p>');
  
    plantilla_editada_form_sd := REPLACE(plantilla_editada_form_sd, '<p style="position:absolute;top:324px;left:94px;white-space:nowrap" class="ft01">Email*:<input type="text" id="email" name="email" size="97" value="$emailCliente" required></p>', '<p style="position:absolute;top:324px;left:94px;white-space:nowrap" class="ft01">Email*:<span class="box" style="width:377px">$emailCliente</span></p>');
     
    plantilla_editada_form_sd := REPLACE(plantilla_editada_form_sd, '<p style="position:absolute;top:414px;left:94px;white-space:nowrap" class="ft01">Direcci&oacute;n*:<input type="text" id="direccion" name="direccion" size="93" value="$direccion" required></p>', '<p style="position:absolute;top:414px;left:94px;white-space:nowrap" class="ft01">Direcci&oacute;n*:<span class="box" style="width:650px">$direccion</span></p>');
  
    plantilla_editada_form_sd := REPLACE(plantilla_editada_form_sd, '<p style="position:absolute;top:438px;left:94px;white-space:nowrap" class="ft01">Provincia*:<input type="text" id="provincia" name="provincia" size="38" value="$provincia" required></p>', '<p style="position:absolute;top:438px;left:94px;white-space:nowrap" class="ft01">Provincia*:<span class="box" style="width:200px">$provincia</span></p>');
  
    plantilla_editada_form_sd := REPLACE(plantilla_editada_form_sd, '<p style="position:absolute;top:438px;left:459px;white-space:nowrap" class="ft01">Ciudad*:<input type="text" id="ciudad" name="ciudad" size="43" value="$ciudad" required></p>', '<p style="position:absolute;top:438px;left:459px;white-space:nowrap" class="ft01">Ciudad*:<span class="box" style="width:200px">$ciudad</span></p>');
  
    plantilla_editada_form_sd := REPLACE(plantilla_editada_form_sd, '<p style="position:absolute;top:463px;left:94px;white-space:nowrap" class="ft01">Tel&eacute;fono*:<input type="text" id="telefono" name="telefono" size="39" value="$telefono" required></p>', '<p style="position:absolute;top:463px;left:94px;white-space:nowrap" class="ft01">Tel&eacute;fono*:&nbsp;<span class="box" style="width:203px">$telefono</span></p>');
    
    plantilla_editada_form_sd := REPLACE(plantilla_editada_form_sd, '<p style="position:absolute;top:463px;left:459px;white-space:nowrap" class="ft01">Celular:<input type="text" id="celular" name="celular" size="44" value="$celular"></p>', '<p style="position:absolute;top:463px;left:459px;white-space:nowrap" class="ft01">Celular:&nbsp;<span class="box" style="width:202px">$celular</span></p>');
   
    plantilla_editada_form_sd := REPLACE(plantilla_editada_form_sd, '<p style="position:absolute;top:542px;left:180px;white-space:nowrap" class="ft01"><input type="radio" name="mes"  checked="$validezCertificadoCheck" />&nbsp;&nbsp;a. 3 d&iacute;as</p>', '<p style="position:absolute;top:542px;left:180px;white-space:nowrap" class="ft01"><span class="box">X</span>&nbsp;&nbsp;a. 3 d&iacute;as</p>');
  
    plantilla_editada_form_sd := REPLACE(plantilla_editada_form_sd, '<p style="position:absolute;top:640px;left:180px;white-space:nowrap" class="ft01"><input type="checkbox" name="uso" value="$usoCertificadoCheck"  style="color: black;background-color: black" checked="checked" />&nbsp;&nbsp;a. Firma de contrato con MEGADATOS</p>', '<p style="position:absolute;top:640px;left:180px;white-space:nowrap" class="ft01"><span class="box">X</span>&nbsp;&nbsp;a. Firma de contrato con MEGADATOS</p>');
   
    plantilla_editada_form_sd := REPLACE(plantilla_editada_form_sd, '<p style="position:absolute;top:764px;left:94px;white-space:nowrap" class="ft00"><b>FIRMA DEL SUSCRIPTOR</b></p>','<p style="position:absolute;top:764px;left:94px;white-space:nowrap" class="ft00"><span><b>FIRMA DEL SUSCRIPTOR</b></span><input id="inputfirma1" name="FIRMA_FORM_SD_CLIENTE" type="text" value="" style="background-color:#fff; width:0.0em; margin-left:0.75em; border-style: hidden; opacity:0; border:none;" readonly/></p>');
   
    UPDATE DB_FIRMAELECT.ADM_EMPRESA_PLANTILLA t SET t.HTML = plantilla_editada_form_sd WHERE t.COD_PLANTILLA = 'formularioSecurityData' AND t.ESTADO = 'Activo';
    
    DBMS_OUTPUT.PUT_LINE('OK formularioSecurityData ' || DBMS_LOB.GETLENGTH(plantilla_editada_form_sd));
  
  
    ------- PAGARE ----- 
    SELECT t.* INTO registro_pagare FROM DB_FIRMAELECT.ADM_EMPRESA_PLANTILLA t WHERE t.COD_PLANTILLA = 'pagareMegadatos' AND t.ESTADO = 'Activo';

    plantilla_editada_pagare := REPLACE(registro_pagare.HTML, '<div id="col" class="col-width-25">Firma:</div>','<div id="col" class="col-width-25"><span>Firma:</span><input id="inputfirma1" name="FIRMA_CONT_MD_PAGARE" type="text" value="" style="background-color:#fff; width:0.0em; margin-left:0.75em; border-style: hidden; opacity:0; border:none;" readonly/></div>');
    
    UPDATE DB_FIRMAELECT.ADM_EMPRESA_PLANTILLA t SET t.HTML = plantilla_editada_pagare WHERE t.COD_PLANTILLA = 'pagareMegadatos' AND t.ESTADO = 'Activo';
    
    DBMS_OUTPUT.PUT_LINE('OK pagareMegadatos ' || DBMS_LOB.GETLENGTH(plantilla_editada_pagare));
    --------------------
  
  
    ------- DEBITO ----- 
    SELECT t.* INTO registro_debito FROM DB_FIRMAELECT.ADM_EMPRESA_PLANTILLA t WHERE t.COD_PLANTILLA = 'debitoMegadatos' AND t.ESTADO = 'Activo';

    plantilla_editada_debito := REPLACE(registro_debito.HTML, '<div id="colCell" class="col-width-20">Firma del Cliente</div>','<div id="colCell" class="col-width-20"><span>Firma del Cliente</span><input id="inputfirma1" name="FIRMA_CONT_MD_AUT_DEBITO" type="text" value="" style="background-color:#fff; width:0.0em; margin-left:0.75em; border-style: hidden; opacity:0; border:none;" readonly/></div>');

    UPDATE DB_FIRMAELECT.ADM_EMPRESA_PLANTILLA t SET t.HTML = plantilla_editada_debito WHERE t.COD_PLANTILLA = 'debitoMegadatos' AND t.ESTADO = 'Activo';
    
    DBMS_OUTPUT.PUT_LINE('OK debitoMegadatos ' || DBMS_LOB.GETLENGTH(plantilla_editada_debito));
    --------------------
    
    ------ ADENDUM ------
    DBMS_LOB.APPEND(plantilla_editada_adendum, '
<html>
<head>
    <title>Requerimientos de Servicios</title>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <style type="text/css">
        * {
            font-family: "Utopia";
        }

        body {
            width: 950px;
            font-size: 11px;
        }

        #bienvenido {
            font-weight: bold;
            font-size: 16px;
            position: absolute;
        }

        #netlife {
            font-size: 9px;
        }

        /* // ==========================================
               // Clases definidas para los componentes
               // ==========================================*/
        #contenedor {
            display: table;
            width: auto;
            vertical-align: middle;
            border-spacing: 0px;
            margin: 0px;
            padding: 0px;
        }

        #row {
            display: table-row;
            vertical-align: middle;
        }

        #col {
            display: inline-block;
            vertical-align: middle;
        }

        #colCell {
            display: table-cell;
            vertical-align: middle;
        }

        /* // ==========================================
               // Clases definidas para los componentes
               // ==========================================*/
        .labelBlock {
            font-weight: bold;
            background: #f9e314;
            font-size: 12px;
            border-top: 1px black solid;
            border-bottom: 1px solid black;
            margin: 1em 0;
            padding-left: 1em;
        }

        label,
        .labelGris {
            background: #E6E6E6;
            border-radius: 3px;
            -moz-border-radius: 2px;
            -webkit-border-radius: 2px;
        }

        .box {
            height: 15px;
            width: 15px;
            border: 1px solid black;
            display: inline-block;
            border-radius: 2px;
            -moz-border-radius: 2px;
            -webkit-border-radius: 2px;
            vertical-align: top;
            text-align: center;
        }

        .box-label {
            padding-left: 3px;
            text-align: center;
            display: inline-block;
            vertical-align: top;
        }

        .line-height,
        .labelBlock,
        #col {
            height: 18px;
            line-height: 18px;
            margin-top: 2px;
        }

        .textLeft {
            text-align: left;
        }

        .textRight {
            text-align: right;
        }

        .textCenter {
            text-align: center;
        }

        .textPadding {
            padding-left: 5px;
        }

        .borderTable th,
        .borderTable td {
            border: 1px solid black;
        }
        
        table tr td {
          font-size: 12px;
        }

        /* // ==========================================
               // Vi&ntilde;etas para las clausulas
               // ==========================================*/
        .clausulas ul {
            list-style: none;
            /* Remove list bullets */
            padding: 0;
            margin: 0;
            list-style-position: inside;
            text-indent: -1em;
        }
        
        .clausulas li {
            padding-left: 16px;
        }

        .clausulas li:before {
            content: "-";
            padding-right: 5px;
        }

        /* // ==========================================
               // Clases de manejo de tama&ntilde;o de columnas
               // ==========================================*/
        .col-width-2-5 {
            width: 2.5% !important;
        }
        .col-width-5 {
            width: 5% !important;
        }

        .col-width-10 {
            width: 10% !important;
        }

        .col-width-15 {
            width: 15% !important;
        }

        .col-width-20 {
            width: 20% !important;
        }

        .col-width-25 {
            width: 25% !important;
        }

        .col-width-30 {
            width: 30% !important;
        }

        .col-width-35 {
            width: 35% !important;
        }

        .col-width-40 {
            width: 40% !important;
        }

        .col-width-45 {
            width: 45% !important;
        }

        .col-width-50 {
            width: 50% !important;
        }

        .col-width-55 {
            width: 55% !important;
        }

        .col-width-60 {
            width: 60% !important;
        }

        .col-width-65 {
            width: 65% !important;
        }

        .col-width-70 {
            width: 70% !important;
        }

        .col-width-75 {
            width: 75% !important;
        }

        .col-width-80 {
            width: 80% !important;
        }

        .col-width-85 {
            width: 85% !important;
        }

        .col-width-90 {
            width: 90% !important;
        }

        .col-width-95 {
            width: 95% !important;
        }

        .col-width-100 {
            width: 100% !important;
        }

        a {
            display: block;
        }
    </style>
</head>

<body>
    <!-- ================================ -->
    <!-- Logo Netlife y numero de contato -->
    <!-- ================================ -->
    
  <div id="contenedor" class="col-width-100" style="font-size:14px;">
    <table class="col-width-100">         
      <tr>     
        <td class="col-width-75" style="font-size:14px;"><b>ADENDUM SERVICIOS / PRODUCTOS ADICIONALES</b></td> 
        <td id="netlife" class="col-width-25" align="center" rowspan="2">
          <img src="http://images.telconet.net/others/telcos/logo_netlife.png" alt="log" title="NETLIFE" height="40"/>
          <div style="font-size:14px">Teléfono fijo nacional 3920000</div>
          <div style="font-size:20px"><b>$!numeroAdendum</b></div>
        </td>
      </tr>
      <tr>
        <td class="col-width-75" style="font-size:14px;">Este documento adenda al contrato $!numeroContrato</td>
      </tr>
    </table>
  </div>
  
    <div id="contenedor" class="col-width-100">
      <div id="row">
        <div id="col" class="col-width-15">Fecha:</div>
        <div id="col" class="col-width-15 labelGris">$!fechaActual<span class="textPadding"></span></div>
      </div>
    </div>
    
    <br/>
    

    <div style="clear: both;"></div>
    <div class="labelBlock">DATOS DEL CLIENTE</div>

    <div id="contenedor" class="col-width-100">
        <div id="row">
            <div id="col" class="col-width-15">Nombre del cliente:</div>
            <div id="col" class="col-width-50 labelGris">$!nombresApellidos<span class="textPadding"></span></div>
            <div id="col" class="col-width-2-5"></div>
            <div id="col" class="col-width-5">CI:</div>
            <div id="col" class="col-width-25 labelGris">$!cedula<span class="textPadding"></span></div>
        </div>
        <div id="row">
            <div id="col" class="col-width-15">Razón social:</div>
            <div id="col" class="col-width-50 labelGris">$!razonSocial<span class="textPadding"></span></div>
            <div id="col" class="col-width-2-5"></div>
            <div id="col" class="col-width-5">RUC:</div>
            <div id="col" class="col-width-25 labelGris">$!ruc<span class="textPadding"></span></div>
        </div>
        <div id="row">
            <div id="col" class="col-width-15">Login:</div>
            <div id="col" class="col-width-50 labelGris">$!loginPunto<span class="textPadding"></span>
        </div>

        </div>
        <div id="row">
            <div id="col" class="col-width-15">Plan actual contratado:</div>
            <div id="col" class="col-width-50 labelGris">$!nombrePlan<span class="textPadding"></span></div>
            <div id="col" class="col-width-2-5"></div>
            <div id="col" class="col-width-5">Correo:</div>
            <div id="col" class="col-width-25 labelGris">$!correoCliente<span class="textPadding"></span>
        </div>

        </div>
        <div id="row">
            <div id="col" class="col-width-100">Dirección: Este servicio/producto adicional se activará en el login y plan actual contratado.</div>
        </div>
        <div id="row">
            <div id="col" class="col-width-100">Forma de pago: El cliente acepta que la forma de pago de los servicios adicionales que contrate tendrán la misma forma de pago que el servicio principal de NETLIFE contratado.</div>
        </div>
    </div>


    <div style="clear: both;"></div><br /><br/>
    <div class="labelBlock">DATOS DE CONTACTO</div>
        <div id="contenedor" class="col-width-100">
          <div id="row">
            <div id="col" class="col-width-10">Persona contacto:</div>
            <div id="col" class="col-width-20 labelGris">$!personaContacto<span class="textPadding"></span></div>
            <div id="col" class="col-width-2-5"></div>
            <div id="col" class="col-width-15">Tel&eacute;fono contacto:</div>
            <div id="col" class="col-width-15 labelGris">$!celularContacto<span class="textPadding"></span></div>
            <div id="col" class="col-width-2-5"></div>
            <div id="col" class="col-width-15">Tel&eacute;fono fijo contacto:</div>
            <div id="col" class="col-width-15 labelGris">$!telefonoContacto<span class="textPadding"></span></div>
        </div>
    </div>
        <div style="clear: both;"></div><br/><br/>
        </div>
        <div style="col-width-100;  vertical-align:top;">
          <div class="labelBlock" style="margin: 0; border:1px black solid;">SERVICIO/PRODUCTO ADICIONAL CONTRATADO</div>
              <table class="box-section-content col-width-100 borderTable" style="border-collapse:collapse; border-spacing:0;">
                <tr>
                  <td class="labelBlock textCenter" style="width: 39%"><b>SERVICIO</b></td>
                  <td class="labelBlock textCenter" style="width: 10%"><b>CANTIDAD</b></td>
                  <td class="labelBlock textCenter" style="width: 13%"><b>VALOR UNICO</b></td>
                  <td class="labelBlock textCenter" style="width: 11%"><b>VALOR MES</b></td>
                  <td class="labelBlock textCenter" style="width: 14%"><b>VALOR TOTAL</b></td>
                  <td class="labelBlock textCenter" style="width: 13%"><b>OBSERVACIONES</b></td>
                </tr>
                
                {{listaProductos}}
                
                <tr>
                    <td class="line-height textCenter" colspan="2" style="border-bottom:1px white solid;">SUBTOTAL:</td>
                    <td class="line-height textCenter">$!subtotalInstalacion1</td>
                    <td class="line-height textCenter">SUBTOTAL:</td>
                    <td class="line-height textCenter">$!subtotal</td>
                    <td class="line-height textCenter" style="border-bottom:1px white solid;"></td>
                </tr>
                <tr>
                    <td class="line-height textCenter" colspan="2" style="border-bottom:1px white solid;">IMPUESTOS:
                    </td>
                    <td class="line-height textCenter">$!impInstalacion1</td>
                    <td class="line-height textCenter">IMPUESTOS:</td>
                    <td class="line-height textCenter">$!impuestos</td>
                    <td class="line-height textCenter" style="border-bottom:1px white solid;"></td>
                </tr>
                <tr>
                    <td class="line-height textCenter" colspan="2">TOTAL:</td>
                    <td class="line-height textCenter">$!totalInstalacion1</td>
                    <td class="line-height textCenter">TOTAL:</td>
                    <td class="line-height textCenter">$total</td>
                    <td class="line-height textCenter"></td>
                </tr>
            </table>
          </div>
        </div>');

    DBMS_LOB.APPEND(plantilla_editada_adendum,'<div style="clear: both;"><br /></div>
        <div class="col-width-100" style="text-align: justify;">
        <div>NETLIFE puede modificar estas condiciones o las condiciones adicionales que se apliquen a un Servicio o Producto con el fin por ejemplo de reflejar cambios legislativos, sustitución y/o mejoras en los Servicios prestados, te recomendamos que consultes las condiciones de forma periódica. La contratación de nuestros Servicios implica la aceptación de las condiciones descritas a estas condiciones. Te recomendamos que las leas detenidamente </div>
                       
            <br />
            <div> <b>INFORMACION Y CONDICIONES ADICIONALES: </b> </div>
            <br />
            <div class="clausulas">
                <ul>
                    <li>El cliente conoce, acepta y suscribe el presente documento de servicios adicionales contratados, el cual forma parte del contrato de adhesión, se obliga con la misma forma de pago suscrito con anterioridad entre el cliente y NETLIFE.</li>
                    <br />
                    <li>El cliente conoce, entiende y acepta que se le ha socializado toda la información referente al servicio(s) / producto(s) adicional(es) contratado(s)  y que está de acuerdo con todos los items descritos en el presente adendum. El cliente conoce, entiende y acepta que el servicio contratado con NETLIFE NO incluye cableado interno o configuración de la red local del cliente e incluye condiciones de permanencia mínima en caso de recibir promociones.</li>
                    <br />
                    <li>Los servicios adicionales generan una facturación proporcional al momento de la contratación y luego será de forma recurrente. El servicio adicional estará activo mientras el cliente esté al día en pagos, caso contrario no podrá acceder al mismo por estar suspendido.</li>  
                </ul>
            </div>
            
            <br /><br />
            <div><b>NETCAM: </b> </div>
            <br />
            <div class="clausulas">
                <ul>
                    <li>El cliente entiende que la instalación del servicio se realizará máximo 10 metros del router WIfi para garantizar su calidad óptima. Esta distancia podría ser menor y dependerá de la cantidad de obstáculos e interferencias de señal que se detecten durante la instalación.</li>
                    <br />
                    <li>La instalación del servicio incluye la visualización de la cámara. No incluye cableado interno, configuración de red local, ni trabajos de conexión eléctrica.</li>
                    <br />
                    <li>La cámara que se instala para este servicio, en el caso de daño por negligencia del Cliente, éste asumirá el valor total de su reposición, valor que será gravado en la facturación. El costo de la cámara es de USD$69,99 (mas IVA).</li>
                    <br />
                    <li>El cliente es reponsable por el uso y seguridad de la clave de acceso a la visualización remota que provea MEGADATOS, por lo que el cliente deslinda  de cualquier responsabilidad por pérdida o mal uso que pueda derivarse del manejo no adecuado que el cliente pueda dar a esta clave a MEGADATOS. El cliente es responsable de realizar una actualización frecuente de su clave para evitar cualquier ataque que pueda sufrir su acceso.</li>
                    <br />
                    <li>El cliente es absolutamente responsable de la información o contenido del servicio contratado, así como de la transmisión de ésta a los usuarios de Internet.</li>
                    <br />
                    <li>El cliente está consciente que es el único responsable de grabar y respaldar la información de su propiedad que pueda derivarse de la visualización remota.</li>
                    <br />
                    <li>El cliente libera y mantiene a salvo a MEGADATOS de los daños y perjuicios que se ocasionen por accesos no autorizados, robo, daño, destrucción o desviación de la información, archivos o programas que se relacionen de manera directa o indirecta con el "Servicio" prestado por MEGADATOS.</li>
                    <br />
                    <li>El cliente libera y mantiene a salvo a MEGADATOS de cualquier reclamación, demanda y/o acción legal que pudiera derivarse del uso que el cliente o terceras personas relacionadas hagan del servicio, que implique daño, alteración y/o modificación a la red, medios y/o infraestructura a través de la cual se presta el servicio.</li>
                </ul>
            </div>

            <br /><br />
            <div><b>NETLIFE CLOUD (MICROSOFT 365 FAMILIA):</b> </div>
            <br />
            <div class="clausulas">
                <ul>
                    <li>Para poder activar este producto es requisito contar con una cuenta microsoft (Cuentas @hotmail.com, @outlook.com, etc ).</li>
                    <br />
                    <li>El producto tiene una vigencia de 12 meses e incluye renovación automática de licencia. En caso de cancelarlo antes de los 12 meses de cualquiera de sus períodos de vigencia y renovación, el cliente entiende, acepta y suscribe que sea facturado el valor proporcional, de acuerdo al tiempo de vigencia que resta por cubrir.</li>
                    <br />
                    <li>Este producto se puede instalar en PCs y tabletas Windows que ejecuten Windows 7 o una versión posterior, y equipos Mac con Mac OS X 10.6 o una versión posterior.  Office para iPad se puede instalar en iPads que ejecuten la última versión de iOS. Office Mobile para iPhone se puede instalar en teléfonos que ejecuten iOS 6.0 o una versión posterior. Office Mobile para teléfonos Android se puede instalar en teléfonos que ejecuten OS 4.0 o una version posterior. Para obtener más información sobre los dispositivos y requerimientos, visite: <b>www.office.com/information</b>.</li>
                    <br />
                    <li>La entrega de este producto no incluye el servicio de instalación del mismo en ningún dispositivo. Para tal efecto, dentro del producto se encuentra una guía de instalación a seguir por el cliente. El cliente es responsable de la instalación y configuración del producto en sus dispositivos y usuarios.</li>
                    <br />
                    <li>El canal de soporte para consultas, dudas o requerimientos específicos del producto Microsoft 365 Familia podrá ser realizado a través del teléfono: 1-800-010-288.</li>
                    <br />
                    <li>Los pasos para instalar y empezar a utilizar Microsoft 365 Familia se encuentran en el siguiente link: office.com/setup. Para administrar los dispositivos y cuentas de su licencia Microsoft 365 Familia el cliente puede acceder al link: office.com/myaccount.</li>
                </ul>
            </div>

            <br /><br />
            <div><b>RENTA DE EQUIPOS: Router WIFI Dual Band y/o AP Extender WiFi Dual Band:</b> </div>
            <br />
            <div class="clausulas">
                <ul>
                    <li>El equipo es propiedad de MEGADATOS S.A. y cuenta con una garantía de 1(UN) año por defectos de fábrica. Al finalizar la prestación del servicio el cliente deberá entregarlo en las oficinas de MEGADATOS. En caso de que el cliente no lo devolviere, se detecte mal uso o daños, el costo total del equipo por reposición será facturado al cliente. En caso del router WiFi Dual Band es de $175,00 (más IVA) y del AP Extender WiFi Dual Band es de $75.00 (más IVA).</li>
                    <br />
                    <li>El cliente conoce y acepta que, para garantizar la calidad del servicio, estos equipos serán administrado por NETLIFE mientras dure la prestación del servicio.</li>
                    <br />
                    <li>El equipo WiFi provisto por NETIFE tiene puertos alámbricos que permiten la utilización óptima de la velocidad ofertada en el plan contratado, además cuenta con conexión WiFi a una frecuencia de 5Ghz que permite una velocidad máxima de 150Mbps a una distancia de 3 metros y pueden conectarse equipos a una distancia de hasta 12 metros en condiciones normales, sin embargo, la distancia de cobertura varía según la cantidad y tipo de paredes, obstáculos e interferencia que se encuentren en el entorno. El cliente conoce y acepta que la tecnología WiFi pierde potencia a mayor distancia y por lo tanto se reducirá la velocidad efectiva a una mayor distancia de conexión del equipo.</li>
                </ul>
            </div>
            
            <br /><br />
            <div><b>NETLIFE DEFENSE:</b> </div>
            <br />
            <div class="clausulas">
                <ul>
                    <li>Netlife Defense es un servicio de seguridad informática que permite reducir los riesgos de vulnerabilidades en la navegación y transacciones por internet..</li>
                    <br />
                    <li>El método de entrega de este servicio es mediante envío de correo electrónico al correo registrado por el cliente en su contrato o en esta solicitud. Este correo debe ser un correo electrónico válido. Es responsabilidad del cliente verificar que el correo no se encuentre alojado en la carpeta de correo no deseado. En caso de requerirlo, el cliente podrá solicitar el reenvio de éste.</li>
                    <br />
                    <li>Para que esta solución de seguridad informática esté en operación, es necesaria la instalación del software en el dispositivo que requiera protegerse.</li>
                    <br />
                    <li>Netlife Defense soporta: Equipos de escritorio y portátiles: Windows 10/8.1 /8 /7 o superior; OS X 10.12 – macOS 10.13 o superiores; Tablets: Windows 10 / 8 & 8.1 / Pro (64 bits); iOS 9.0 o posterior; Smartphones: Android 4.1 o posterior, iOS 9.0 o posterior (solo para navegación; a través, de Kaspersky Safe Browser).</li>
                    <br />
                    <li>Requerimientos mínimos del sistema: Disco Duro: Windows: 1.500 MB; Mac 1220 MB. Memoria (RAM) libre:1 GB (32 bits) o 2 GB (64 bits). Resolución mínima de pantalla 1024x600 (para tablets con Windows), 320x480 (para dispositivos Android). Conexión Activa a Internet.</li>
                </ul>
            </div>
            
            <br /><br />
            <div><b>NETLIFEZONE:</b> </div>
            <br />
            <div class="clausulas">
                <ul>
                    <li>Netlife Zone es un servicio de conexión a Internet por WiFi en zonas abiertas tipo "Best Effort" que permite una velocidad máxima de hasta 20Mbps.</li>
                    <br />
                    <li>El servicio incluye un usuario y contraseña para conectarse a la red WiFi y brinda una conexión simultánea desde un dispositivo.</li>
                    <br />
                    <li>El servicio esta sujeto a la cobertura de la red WiFi cuyo nombre SSID es #Netlifezone y no garantiza distáncia ni ancho de banda de conexión.</li>
                    <br />
                    <li>El mapa de cobertura, la información sobre características, funcionalidades y restricciones se encuentra disponible en www.netlife.ec.</li>
                    <br />
                    <li>El cliente conoce, acepta y suscribe las condiciones de este servicio en función de los parámetros y características definidas en el presente instrumento.</li>
                </ul>
            </div>
            
            <br /><br />
            <div><b>NETLIFE ASSISTANCE:</b> </div>
            <br />
            <div class="clausulas">
                <ul>
                    <li>NetlifeAssistance es un servicio que brinda soluciones remotas ilimitadas de asistencia técnica para equipos terminales del cliente, entre los cuales están: Asistencia guiada de configuración e instalación de software o hardware; Revisión, análisis y mantenimiento del PC/MAC; Asesoría técnica en línea las 24 horas  del PC/MAC; Técnico PC y dispositivos remoto ilimitado; Hasta 3 visitas presenciales al año; un traslado/reubicación al año; valor normal: $8.75+iva mensual.</li>
                    <br />
                    <li>El servicio no incluye materiales, sin embargo si el cliente los requiere se cobrarán por separado. Tampoco incluye reparación de equipos o dispositivos.</li>
                    <br />
                    <li>El servicio tiene un tiempo de permanencia mínima de 12 meses. En caso de cancelación anticipada aplica la clausula de pago de los descuentos a los que haya accedido por exoneración, tales como Instalación, tarifas preferenciales, etc.</li>
                    <br />
                    <li>El servicio aplica para planes hogar de las ciudades de Quito y Guayaquil.</li>
                </ul>
            </div>
            
            <br /><br />
            <div><b>NETLIFE ASSISTANCE PRO:</b> </div>
            <br />
            <div class="clausulas">
                <ul>
                    <li>Netlife Assistance PRO es un servicio que brinda soluciones a los problemas técnicos e informáticos de un negocio para mejorar su operación, este servicio incluye: Asistencia guiada de configuración, sincronización y conexión a red de software o hardware: PC, MAC; Revisión, análisis y mantenimiento del PC/MAC/LINUX/SmartTV/Smartphones/Tablets/Apple TV/Roku, etc.; Asesoría técnica en línea las 24 horas vía telefónica o web por store.netlife.net.ec; Un servicio de Help Desk con ingenieros especialistas.</li>
                    <br />
                    <li>No incluye capacitación en el uso del Sistema Operativo y software, únicamente se solucionarán incidencias puntuales.</li>
                    <br />
                    <li>El servicio tiene un tiempo de permanencia mínima de 12 meses. En caso de cancelación anticipada aplica la clausula de pago de los descuentos a los que haya accedido por exoneración, tales como Instalación, tarifas preferenciales, etc.</li>
                    <br />
                    <li>Se puede ayudar a reinstalar el Sistema Operativo del dispositivo del cliente, siempre y cuando se disponga de las licencias y medios de instalación originales correspondientes.</li>
                    <br />
                    <li>Sistemas Operativos sobre los cuales se brinda soporte a incidencias: Windows: XP hasta 10, Windows Server: 2003 hasta 2019, MacOs: 10.6 (Snow Leopard) hasta 10.14 (Mojave), Linux: Ubuntu 19.04, Fedora 30, Open SUSE 15.1, Debian 10.0, Red Hat 8, CentOS 7, iOS: 7.1.2 a 12.3.2, Android: Ice Cream Sandwich 4.0 hasta Pie 9.0, Windows Phone OS: 8.0 hasta 10 Mobile.</li>
                    <br />
                    <li>Asistencia Hardware: Los controladores o software necesarios para el funcionamiento del hardware son responsabilidad del usuario, aunque prestaremos todo nuestro apoyo para obtenerlos en caso necesario.  Asistencia Software: No incluye capacitación en el uso del Software. Las licencias y medios de instalación son a cargo del usuario. Nunca se prestará ayuda sobre software ilegal.</li>
                    <br />
                    <li>Se mantendrá en la plataforma durante 60 días, el 100% de las conversaciones chat levantadas vía web; a través de, store.netlife.net.ec.</li>
                </ul>
            </div>
            
            <br /><br />
            <div><b>CONSTRUCTOR WEB:</b> </div>
            <br />
            <div class="clausulas">
                <ul>
                    <li>Constructor Web es un servicio que te permite construir tu propia página web, tener 1 dominio propio y 5 cuentas de correo asociadas a este dominio. Además de, asesoría técnica en línea las 24 horas vía telefónica o web por store.netlife.net.ec .¿ La propiedad del dominio está condicionada a un tiempo de permanencia mínima de 12 meses y se renueva anualmente. En caso de cancelarlo antes de los 12 meses de cualquiera de sus períodos de vigencia y renovación, el cliente entiende y acepta que sea facturado el valor proporcional, de acuerdo con el tiempo de vigencia que resta por cubrir. Es responsabilidad del cliente tomar las medidas necesarias para almacenar la información colocada en su página web.</li>
                    <br />
                    <li>Se incluye el servicio de diseño de la página web por parte del equipo de diseño bajo solicitud del usuario y sujeto al envío de la información relevante para su creación. El servicio incluye hasta 5 páginas de contenido, formulario de contacto para recibir comunicación de los visitantes a un correo especificado, links a las redes sociales, mapa de Google interactivo, conexión con Google Analytics. El tiempo de entrega/publicación estimado es de 5 días hábiles, pero está sujeto al envío oportuno de información del cliente, así como del volumen de material recibido.</li>
                    <br />
                    <li>Webmail: Administración de correos, carpetas, y filtros con una interfaz intuitiva y fácil de utilizar proporcionada por Roundcube. Se puede agregar cualquier cuenta IMAP/POP para tener una única interfaz.</li>
                    <br />
                    <li>Navegadores Soportados: Windows Vista, 7, y 8 | IE 9.0 en adelante  | Firefox versión 19 en adelante. | Google Chrome versión 25 en adelante  | Windows 10 | Edge 12 en adelante | Mac OS X 10.4, 10.5, y 10.6 | Firefox versión 19 en adelante | Safari versión 4.0 en adelante.</li>
                    <br />
                    <li>Se considera “spam” la práctica de enviar mensajes de correo electrónico no deseados, a menudo con contenido comercial, en grandes cantidades a los usuarios, sin darles la opción de darse de baja o excluirse de una lista de distribución. Por lo anterior, queda prohibido que el cliente use el correo para estos fines. En caso de cualquier violación a estas Políticas, se procederá a tomar una de las siguientes medidas: 1ro Suspender/Bloquear la cuenta por un lapso de 72 horas. -  2do Suspender/Bloquear la cuenta por un lapso de 144 horas. - 3ro Suspender/Bloquear todo tráfico del dominio y se iniciará el proceso de baja de servicio.</li>
                    <br />
                    <li>El acceso al servicio es posible desde store.netlife.net.ec.</li>
                </ul>
            </div>
            
            <br /><br />
            <div><b>CUENTAS DE CORREO:</b> </div>
            <br />
            <div class="clausulas">
                <ul>
                    <li>Es un servicio de correo electrónico  personalizado diseñado para cumplir con todos los estándares de seguridad y productividad. Incluye: 5 cuentas de correo.</li>
                    <br />
                    <li>Compatible con cualquier dominio (*dominio no incluido), accesible desde cualquier lugar y cualquier dispositivo y disponible a través de las plataformas de correo preferidas: Outlook, Gmail, Mail, Thunderbird.</li>
                    <br />
                    <li>Detalles técnicos: Capacidad de 1 Gb Almacenamiento por cuenta, Archivos adjuntos: hasta 25 Mb por correo enviado.</li>
                    <br />
                    <li>Seguridad: Utiliza conexiones seguras (POP3/IMAP/SMTP sobre TLS, webmail con HTTPS). Los correos en tránsito se encriptan con TLS cuando es posible. Las contraseñas se guardan encriptadas en SSHA512 o BCRYPT (BSD).</li>
                    <br />
                    <li>Webmail: Administración de correos, carpetas, y filtros con una interfaz intuitiva y fácil de utilizar proporcionada por Roundcube. Se puede agregar cualquier cuenta IMAP/POP para tener una única interfaz.</li>
                    <br />
                    <li>Respaldos: Respaldos diarios de todos los correos para evitar pérdida de datos.</li>
                    <br />
                    <li>El servicio no incluye mantenimientos programados a las plataformas que soportan al correo y mantenimientos no programados para solventar situaciones críticas.</li>
                    <br />
                    <li>Se considera "spam" la práctica de enviar mensajes de correo electrónico no deseados, a menudo con contenido comercial, en grandes cantidades a los usuarios, sin darles la opción de darse de baja o excluirse de una lista de distribución. Por lo anterior, queda prohibido que el cliente use el correo para estos fines. En caso de cualquier violación a estas Políticas, se procederá a tomar una de las siguientes medidas: 1ro Suspender/Bloquear la cuenta por un lapso de 72 horas. -  2do Suspender/Bloquear la cuenta por un lapso de 144 horas. - 3ro suspender/bloquear todo tráfico del dominio y se iniciará el proceso de baja de servicio.</li>
                    <br />
                    <li>Servicio sólo disponible para planes PYME, el acceso al servicio es posible desde store.netlife.net.ec.</li>
                </ul>
            </div>
            
            <br /><br />
            <div>
              El cliente con la sola suscripción voluntaria del presente Adendum confirma que ha leído y conoce las condiciones de uso de los equipos y servicios descritos a ser contratados. La información proporcionada en el presente documento, el cliente autoriza a Megadatos para uso y tratamiento  acorde a la normativa legal vigente a fin al contrato de adhesión.
            </div>

        </div>
        
        <div style="clear: both;"></div><br /><br /><br />
        <br />
        <div style="clear: both;"></div>
        <div id="contenedor" class="col-width-100">
            <div id="row">
                <div id="colCell" class="col-width-50" style="text-align:center">
                    <div id="contenedor" class="col-width-100">
                        <div id="row">
                            <div id="colCell" class="col-width-25"></div>
                            <div id="colCell" class="col-width-50" style="height:35px">

                            </div>
                            <div id="colCell" class="col-width-25"></div>
                        </div>
                        <div id="row">
                            <div id="colCell" class="col-width-25"></div>
                            <div id="colCell" class="col-width-50">
                                <hr>
                            </div>
                            <div id="colCell" class="col-width-25"></div>
                        </div>
                        <div id="row">
                            <div id="colCell" class="col-width-25"></div>
                            <div id="colCell" class="col-width-50"><span>MEGADATOS</span><input id="inputfirma1" name="FIRMA_ADEN_MD_EMPRESA" type="text" value="" style="background-color:#fff; width:0.0em; margin-left:0.75em; border-style: hidden; opacity:0; border:none;" readonly/></div>
                            <div id="colCell" class="col-width-25"></div>
                        </div>
                    </div>
                </div>
                <div id="colCell" class="col-width-50" style="text-align:center">
                    <div id="contenedor" class="col-width-100">
                        <div id="row">
                            <div id="colCell" class="col-width-25"></div>
                            <div id="colCell" class="col-width-50" style="height:35px">

                            </div>
                            <div id="colCell" class="col-width-25"></div>
                        </div>
                        <div id="row">
                            <div id="colCell" class="col-width-25"></div>
                            <div id="colCell" class="col-width-50">
                                <hr>
                            </div>
                            <div id="colCell" class="col-width-25"></div>
                        </div>
                        <div id="row">
                            <div id="colCell" class="col-width-25"></div>
                            <div id="colCell" class="col-width-50"><span>Firma del Cliente</span><input id="inputfirma2" name="FIRMA_ADEN_MD_CLIENTE" type="text" value="" style="background-color:#fff; width:0.0em; margin-left:0.75em; border-style: hidden; opacity:0; border:none;" readonly/></div>
                            <div id="colCell" class="col-width-25"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div style="clear: both;"></div><br /><br />
        <br />
        <div style="clear: both;"></div>
        <div id="contenedor" class="col-width-100">
            <div id="row">
                <div id="colCell" class="col-width-50" style="text-align:right">ver-06 | Mar-2019</div>
            </div>
        </div>
</body>

</html>');

    UPDATE DB_FIRMAELECT.ADM_EMPRESA_PLANTILLA t SET t.HTML= plantilla_editada_adendum where t.COD_PLANTILLA = 'adendumMegaDatos' AND ESTADO = 'Activo';
    DBMS_OUTPUT.PUT_LINE('OK adendumMegaDatos ' || DBMS_LOB.GETLENGTH(plantilla_editada_adendum));
    ---------------------

    COMMIT;
    DBMS_OUTPUT.put_line('-OK-');
EXCEPTION
    WHEN OTHERS THEN
        ROLLBACK;
        DBMS_OUTPUT.put_line (DBMS_UTILITY.format_error_backtrace);   
END;
/