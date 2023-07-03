UPDATE DB_FIRMAELECT.ADM_EMPRESA_PLANTILLA
SET HTML=EMPTY_CLOB()
where COD_PLANTILLA = 'contratoMegadatos';
commit;

SET SERVEROUTPUT ON 200000;
declare
    bada clob:='<!DOCTYPE html>';
begin

--DBMS_LOB.FREETEMPORARY(bada);

--dbms_lob.createtemporary(bada, TRUE);
DBMS_LOB.APPEND(bada, '
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
               // Vi&' || 'ntilde;etas para las clausulas
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
               // Clases de manejo de tama&' || 'ntilde;o de columnas
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
    </head>');
    DBMS_LOB.APPEND(bada, ' <body>
        <div id="bienvenido">CONTRATO DE ADHESI&' || 'Oacute;N DE PRESTACI&' || 'Oacute;N DE SERVICIOS DE ACCESO A INTERNET/PORTADOR</div>

        <!-- ================================ -->
        <!-- Logo Netlife y numero de contato -->
        <!-- ================================ -->
        <div align="center" style="float: right;">
            <!--<img src="http://images.telconet.net/others/telcos/logo_netlife.png" alt="log" title="NETLIFE"  height="40"/>-->


            <table id="netlife" style="padding-right: 30px; ">
                <tr>
                    <td align="center" style ="font-size:14px">
                    <img src="http://apps.telconet.ec/others/telcos/logo_netlife.png" alt="log" title="NETLIFE" height="40"/>
                    <br/>
                    37-31-300 &' || 'oacute; al
                    </td>
                </tr>
                <tr><td align="center" style ="font-size:14px">1-700 NETLIFE (638-543)</td></tr>
                <tr></tr>
                <tr><td align="center" style="font-size:20px">$numeroContrato</td></tr>
            </table>
        </div><!-- ============================ -->
        <!-- Datos iniciales del Contrato -->
        <!-- ============================ -->
        <div style="clear: both;"></div>
        <div id="contenedor" class="col-width-60" style="margin-top:-50px;  ">
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
        <div class="labelBlock">CONTRATO DE ADHESI&' || 'Oacute;N DE PRESTACI&' || 'Oacute;N DE SERVICIOS DE ACCESO A INTERNET/PORTADOR</div>
        <div>
            <span style="font-size:11px">
                <b>Primera:</b> En la ciudad de $!ciudadServicio a los $!diaActual del mes de $!mesActual Celebran el presente Contrato de Adhesi&' || 'oacute;n de Prestaci&' || 'oacute;n de Servicios de Acceso a
                Internet/Portador; 1) por una parte MEGADATOS S.A., compañ&' || 'iacute;a constituida bajo las leyes de la Rep&' || 'uacute;blica del Ecuador, cuyo objeto social constituye entre
                otros, la prestaci&' || 'oacute;n de servicios de telcomunicaciones. Mediante resolución SNT-2010-085 del 30 de marzo del 2010 se autoriz&' || 'oacute; la renovaci&' || 'oacute;n del
                permiso para la prestaci&' || 'oacute;n del servicio de valor agregado de acceso a la red de internet, permiso suscrito el 8 de abril del 2010 e inscrito en el tomo 8S a
                fojas 8503 del registro p&' || 'uacute;blico de telecomunicaciones, cuyo nombre Comercial es NETLIFE 1. en adelante denominado simplemente MEGADATOS, cuyo
                nombre comercial es NETLIFE, ubicada en la calle N&' || 'uacute;ñez de Vela y Atahualpa-Torre del Puente, en la provincia de Pichincha, cant&' || 'oacute;n Quito, ciudad de
                Quito, Parroquia Iñaquito, Tel&' || 'eacute;fonos: 0237-31-300, RUC: 1791287541001, mail: info@netlife.net.ec, web:www.netlife.ec/puntos-de-atencion/ 2) por otra
                parte el ABONADO, cuyos datos se datallan a contiuaci&' || 'oacute;n:
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
                <div id="col" class="col-width-15"><b>Actividad Econ&' || 'oacute;mica:</b></div>
                <div id="col" class="col-width-45 labelGris">
                    <span class="textPadding">$!actividadEconomica</span>
                </div>
                <div id="col" class="col-width-5"></div>
                <div id="col" class="col-width-10"><b>Origen Ingresos:</b></div>
                <div id="col" class="col-width-25 labelGris">
                    <span class="textPadding">$!origenIngresos</span>
                </div>
            </div>
        </div>');
    DBMS_LOB.APPEND(bada, '

        <!-- ======================================== -->
        <!--        Datos del Cliente - Ubicacion     -->
        <!-- ======================================== -->        <div style="clear: both;"></div>
        <div id="contenedor" class="col-width-100" >
            <div id="row">
                <div id="col" class="col-width-20"></div>
                <div id="col" class="col-width-80" style="font-style: oblique; padding-top: 5px;">Formato: Calle Principal,Numeraci&' || 'oacute;n,Calle Secundaria,Nombre Edficio o Conjunto,Piso,Numero
                de Departamento o Casa</div>
            </div>
            <div id="row">
                <div id="col" style="width:17%">
                    <b>Direcci&' || 'oacute;n estado de cuenta: </b>
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
                <div id="col" class="col-width-10"> <b>Cant&' || 'oacute;n: </b> </div>
                <div id="col" class="col-width-20 labelGris">
                    <span class="textPadding">$parroquia</span>
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
                <div id="col" class="col-width-10"> <b>Tipo Ubicaci&' || 'oacute;n: </b> </div>
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
                <div id="col" class="col-width-20"> <b>Tel&' || 'eacute;fono: </b> </div>
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
                <div id="col" class="col-width-10"> <b>Tel&' || 'eacute;fono: </b> </div>
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
                <div id="col" class="col-width-10"> <b>Tel&' || 'eacute;fono: </b> </div>
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
                <div id="col" class="col-width-10"> <b>C&' || 'oacute;digo: </b> </div>
                <div id="col" class="col-width-35 labelGris">
                    <span class="textPadding">$codigoVendedor</span>
                </div>
            </div>
        </div>
        <br/>

        <div>
            <span style="font-size:11px">
                <b>SEGUNDA.-</b> PRESTACI&' || 'Oacute;N DEL SERVICIO: MEGADATOS se compromete a proporcionar al ABONADO el acceso a redes nacionales e internacionales de
                Internet de manera que el mismo disfrute de los servicios y funciones prestados por dichas redes. Se deja expresa constancia que MEGADATOS se
                responsabiliza &' || 'uacute;nica y exclusivamente del acceso a las redes de Internet, por &' || 'eacute;ste motivo no resulta de su responsabilidad el contenido de la informaci&' || 'oacute;n a
                la que pueda accederse, ni el almacenamiento de la misma, incluido el correo electr&' || 'oacute;nico. Las caracter&' || 'iacute;sticas del servicio objeto de este contrato, as&' || 'iacute; como
                las caracter&' || 'oacute;sticas m&' || 'iacute;nimas que requiere el equipo y otros que deben ser garantizados por el ABONADO constan en el anverso de este contrato.
            </span>
        </div>
        <!-- ======================================== -->
        <!--        Datos del Servicio                -->
        <!-- ======================================== -->
        <br/>
        <div style="clear: both;"></div>
        <div class="labelBlock">DATOS DEL SERVICIO</div>
        <div id="contenedor" class="col-width-100" >
            <div id="row">
                <div id="col" class="col-width-35">&' || 'iquest;Los datos de instalaci&' || 'oacute;n son los mismos que los datos del cliente?</div>
                <div id="col" class="col-width-20">
                    <div class="box-label">Si</div>
                    <div class="box">$!isSi</div>

                    <div class="box-label">No</div>
                    <div class="box">$!isNo</div>
                </div>
            </div>
            <div id="row">
                <div id="col" class="col-width-20"></div>
                <div id="col" class="col-width-80" style="font-style: oblique; padding-top: 5px;">Formato: Calle Principal,Numeraci&' || 'oacute;n,Calle Secundaria,Nombre Edficio o Conjunto,Piso,Numero
                de Departamento o Casa</div>
            </div>
            <div id="row">
                <div id="col" style="width:17%">
                    <b>Direcci&' || 'oacute;n estado de cuenta: </b>
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
                <div id="col" class="col-width-10"> <b>Tipo Ubicaci&' || 'oacute;n: </b> </div>
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
        </div>
        <!-- ============================== -->
        <!--      Servicios Contratados     -->
        <!-- ============================== -->');
    DBMS_LOB.APPEND(bada, '
        <div style="clear: both;"><br/><br/></div>
        <div class="labelBlock">SERVICIOS CONTRATADOS (ANEXO T&' || 'Eacute;CNICO)</div>
        <div style="width:36%; float:left; vertical-align:top;" >
            <div class="labelBlock textCenter" style="margin: 0; border:1px black solid;">CARACTER&' || 'Iacute;STICAS DEL PLAN</div>
            <table class="box-section-content col-width-100 borderTable" style="border-collapse:collapse;border-spacing:0;">
                <tr>
                    <td class="col-width-60 line-height" colspan="2">
                         COMPARTICI&' || 'Oacute;N:

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
                    <td class="col-width-10 line-height textCenter " rowspan="2" colspan="3">Acepto los beneficios de promociones vinculados con la cl&' || 'aacute;usula 11 de tiempo m&' || 'iacute;nimo de permanencia.</td>
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
                    <td class="line-height labelGris">ACCESO Y NAVEGACI&' || 'Oacute;N DE INTERNET</td>
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
                </tr>              <tr>');
    DBMS_LOB.APPEND(bada, '
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
                        <div id="colCell" class="col-width-10 textRight"><b>Promoci&' || 'oacute;n:</b></div>
                        <div>$!descInstalacion</div>
                        <div id="colCell" class="col-width-30 textRight">Descuento instalaci&' || 'oacute;n:</div>
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
        <!-- ========================================== -->
        <div style="clear: both;"></div>
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
                    Los siguientes requerimientos podr&' || 'aacute;n ser brindados por un valor adicional:
                    <br/>
                </span>
            </div>
            <div class="clausulas" >
                <ul>
                    <li>Obras civiles o cambios de acometida,sujeto a factibilidad.</li>
                    <li>Despu&' || 'eacute;s de 300metros de cableado de fibra &' || 'oacute;ptica de &' || 'uacute;ltima milla, cada metro adicional de cableado de fibra &' || 'oacute;ptica tendr&' || 'aacute; un valor adicional de $1,00+ impuestos de ley. Sujeto a factibilidad.</li>
                    <li>Nuevas contrataciones, cambios de plan, reactivaciones, cesi&' || 'oacute;n de derechos, traslado f&' || 'iacute;sico del servicio a otro domicilio o reubicaci&' || 'oacute;n en el mismo domicilio,sujeto a factibilidad.</li>
                    <li>Nuevas contrataciones podr&' || 'aacute;n ser solicitadas mediante correo con firma digital a info@netlife.ec,llamando al 37-31-300 &' || 'oacute; al 1-700 NETLIFE (638-543), donde la llamada ser&' || 'aacute; grabada o visit&' || 'aacute;ndonos a nuestros centros de atenci&' || 'oacute;n al cliente, cuyos horarios de atenci&' || 'oacute;n se encuentran en: http://www.netlife.ec/puntos-de-atencion/ </li>
                    <li>Asistencia t&' || 'eacute;cnica a domicilio por solicitud del cliente y debido a causas no imputables a MEGADATOS S.A. en la provisi&' || 'oacute;n del servicio de Internet.</li>
                </ul>
            </div>
            <div> <b>CONDICIONES ADICIONALES: </b> <br/> </div>
            <br/>
            <div class="clausulas" >
                <ul>
                    <li>El tiempo de instalaci&' || 'oacute;n promedio del servicio es de 7 d&' || 'iacute;as h&' || 'aacute;biles, sin embargo, puede variar. El servicio esta sujeto a factibilidad, disponibilidad t&' || 'eacute;cnica y cobertura de red. No incluye obras civiles o cambios de acometida. El contrato entrar&' || 'aacute; en vigencia una vez instalado el servicio y la fecha de activaci&' || 'oacute;n del mismo estar&' || 'aacute; especificada en la factura correspondiente. El cliente acepta y se obliga a estar presente o delegar a un adulto capaz para recibir el servicio el momento de la instalaci&' || 'oacute;n. MEGADATOS no se hace responsable por p&' || 'eacute;rdidas o da&' || 'ntilde;os que puedan derivarse de la falta de cliente o un adulto responsable de recibir el servicio.</li>
                    <li>La instalaci&' || 'oacute;n del servicio incluye un punto de acometida donde se colocar&' || 'aacute; el CPE y Router WiFi que ser&' || 'aacute;n administrados exclusivamente por MEGADATOS. No se podr&' || 'aacute;n retirar,desinstalar o sustituir los equipos proporcionados por MEGADATOS o modificar la configuraci&' || 'oacute;n de los mismos. De ninguna manera se podr&' || 'aacute; revender, repartir o compartir el servicio a trav&' || 'eacute;s de cualquier mecanismo f&' || 'iacute;sico o inal&' || 'aacute;mbrico o a trav&' || 'eacute;s de la compartici&' || 'oacute;n de claves de acceso a terceros, no se podr&' || 'aacute; instalar servidores con ning&' || 'uacute;n tipo de aplicativos, ni c&' || 'aacute;maras de video para video vigilancia o para video streaming para fines comerciales. Para disponer de estos servicios el cliente deber&' || 'aacute; contratar el plan que contemple aquello, el incumplimiento de estas condiciones ser&' || 'aacute; causal de terminaci&' || 'oacute;n de contrato en forma inmediata, bastando la notificaci&' || 'oacute;n del incumplimiento con la informaci&' || 'oacute;n de monitoreo respectivo, sin eximir de la cancelaci&' || 'oacute;n de las deudas pendientes, devoluci&' || 'oacute;n de equipos y valores de reliquidaci&' || 'oacute;n por plazo de permanencia m&' || 'iacute;nima.</li>
                    <li>La instalaci&' || 'oacute;n del servicio incluye la configuraci&' || 'oacute;n para dejar navegando en internet 1 dispositivo. No incluye cableado interno.</li>
                    <li>El cliente es responsable de la instalaci&' || 'oacute;n y configuraci&' || 'oacute;n interna de su red de &' || 'aacute;rea local, as&' || 'iacute; como del control de la informaci&' || 'oacute;n y navegaci&' || 'oacute;n que realice por internet MEGADATOS pone a disposici&' || 'oacute;n de los clientes un servicio integral de seguridad inform&' || 'aacute;tica para reducir el potencial acceso a informaci&' || 'oacute;n que pueda herir la susceptibilidad o que pueda ser fuente de amenazas ciberne&' || 'eacute;ticas. Este servicio puede ser activado por el cliente por un precio adicional seg&' || 'uacute;n se indique en los planes de la p&' || 'aacute;gina web de MEGADATOS y es responsable de su instalaci&' || 'oacute;n en sus equipos terminales.</li>
                    <li>El cliente entiende que s&' || 'oacute;lo podr&' || 'aacute; requerir IPs p&' || 'uacute;blicas est&' || 'aacute;ticas en planes PYME, sin embargo acepta que la direcci&' || 'oacute;n IP asignada podr&' || 'iacute;a modificarse por traslados, cambios de plan o mejoras tecnol&' || 'oacute;gicas, motivos en los cu&' || 'aacute;les existir&' || 'aacute; una coordinaci&' || 'oacute;n previa para generar el menor impacto posible.</li>
                    <li>El servicio HOME s&' || 'oacute;lo es para el segmento residencial, el servicio PYME para profesionales Home/Office que cuenten con m&' || 'aacute;ximo 5 equipos y para empresas (no disponibles para Cybers y/o ISPs). El incumplimiento de estas condiciones se convierte en causal de terminaci&' || 'oacute;n unilateral de contrato.</li>
                    <li>El cliente acepta que MEGADATOS en planes de Internet, para evitar el SPAM, mantenga restringido el puerto 25 (salvo PYME) y para proteger su servicio de posibles ataques y preservar la seguridad de la red restrinja puertos normalmente usados para este fin como son: 135, 137, 138, 139, 445, 593, 1434, 1900, 5000.</li>
                    <li>Los planes de NETLIFE no incluyen cuentas de correo electr&' || 'oacute;nico. En caso de que el cliente lo solicite es posible agregar una cuenta de correo electr&' || 'oacute;nico con dominio netlife.ec por un valor adicional. Esta cuenta de correo no incluye el almacenamiento del mismo, sino que es el cliente quien deber&' || 'aacute; almacenar los correos que lleguen a su cuenta. MEGADATOS no se responsabiliza de ninguna forma por la perdida de almacenamiento de ning&' || 'uacute;n contenido o informaci&' || 'oacute;n.</li>
                    <li>El equipo WiFi provisto tiene puertos al&' || 'aacute;mbricos que permiten la utilizaci&' || 'oacute;n &' || 'oacute;ptima de la velocidad ofertada en el plan contratado, adem&' || 'aacute;s cuenta con conexi&' || 'oacute;n WiFi en planes HOME y PYME, a una frecuencia de 2.4Ghz que permite una velocidad m&' || 'aacute;xima de 30Mbps a una distancia de 3mts y pueden conectarse equipos a una distancia de hasta 15metros en condiciones normales, sin embargo, la distancia de cobertura var&' || 'iacute;a seg&' || 'uacute;n la cantidad de paredes, obst&' || 'aacute;culos e interferencia que se encuentren en el entorno. La cantidad m&' || 'aacute;xima de dispositivos simultaneos que soporta el equipo WiFi son de 15. El cliente conoce y acepta esta especificaci&' || 'oacute;n t&' || 'eacute;cnica y que la tecnolog&' || 'iacute;a WiFi pierde potencia a mayor distancia y por lo tanto se reducir&' || 'aacute; la velocidad efectiva a una mayor distancia de conexi&' || 'oacute;n del equipo.</li>
                    <li>Los equipos terminales y cualquier equipo adicional que eventualmente se instalen (CPE) son propiedad de MEGADATOS. En el caso de da&' || 'ntilde;o por negligencia del Cliente, &' || 'eacute;ste asumir&' || 'aacute; el valor total de su reposici&' || 'oacute;n considerando el deterioro normal y depreciaci&' || 'oacute;n del mismo. Para el caso de servicios FTTH son equipos ONT y WIFI, en el caso de brindar servicios DSL s&' || 'oacute;lo ser&' || 'aacute; el WIFI y en otros medios s&' || 'oacute;lo ser&' || 'aacute; el CPE que tendr&' || 'aacute; el mismo costo del ONT. El costo es de USD$85 (mas IVA) del ONT, USD$40 (mas IVA) para el equipo WiFi 2.4Ghz, USD$175 (mas IVA) para el ONT+WiFi Dual Band y USD$75 (mas IVA) para el equipo AP Extender WiFi Dual Band, los cu&' || 'aacute;les deben incluir sus respectivas fuentes. En caso de p&' || 'eacute;rdida de las fuentes, tienen un costo de USD$10,00 cada una. </li>
                    <li>Disponibilidad del servicio 98%. El tiempo promedio de reparaci&' || 'oacute;n mensual de todos los clientes de NETLIFE es de 24 horas de acuerdo a la normativa vigente, e inicia despu&' || 'eacute;s de haberlo registrado con un ticket en los canales de atenci&' || 'oacute;n al cliente de NETLIFE, se excluye el tiempo imputable al cliente.</li>
                    <li>En caso de reclamos o quejas, el tiempo m&' || 'aacute;ximo de respuesta es de 7 d&' || 'iacute;as despu&' || 'eacute;s de haberlas registrado con un ticket en los canales de atenci&' || 'oacute;n de NETLIFE.</li>
                    <li>Los canales de atenci&' || 'oacute;n al cliente de NETLIFE son: 1) Call Center 2) Centros de Atenci&' || 'oacute;n al cliente de NETLIFE 3) P&' || 'aacute;gina web. 4) Redes sociales. La informaci&' || 'oacute;n de estos canales se encuentra actualizada en la p&' || 'aacute;gina web de NETLIFE www.netlife.ec</li>
                    <li>De acuerdo con la norma de calidad para la prestaci&' || 'oacute;n de servicios de internet, para reclamos de velocidad de acceso el cliente deber&' || 'aacute; realizar los siguientes pruebas: 1) Realizar 2 o 3 pruebas de velocidad en canal vacio, en el veloc&' || 'iacute;metro provisto por NETLIFE y guardarlas en un archivo gr&' || 'aacute;fico. 2) Contactarse con el call center de NETLIFE para abrir un ticket y enviar los resultados de las pruebas.</li>
                    <li>La atenci&' || 'oacute;n telef&' || 'oacute;nica del Call Center es 7 d&' || 'iacute;as,24 horas incluyendo fines de semana y feriados. El soporte presencial es en d&' || 'iacute;as y horas laborables.</li>
                    <li>Cualquier cambio referente a la informaci&' || 'oacute;n de la factura o el servicio deber&' || 'aacute; notificarse 15 d&' || 'iacute;as antes de la finalizaci&' || 'oacute;n del ciclo de facturaci&' || 'oacute;n.</li>
                    <li>MEGADATOS facturar&' || 'aacute; y cobrar&' || 'aacute; al ABONADO el servicio contratado en forma mensual basado en el ciclo de facturaci&' || 'oacute;n en que haya sido definido.  Para ejecutar cancelaciones de servicio o downgrades, el ABONADO deber&' || 'aacute; notificar con 15 d&' || 'iacute;as de anticipaci&' || 'oacute;n a la fecha de finalizaci&' || 'oacute;n de su ciclo de facturaci&' || 'oacute;n.</li>
                    <li>El cliente acepta el pago del valor de $1,00 por los reprocesos y cargos bancarios que se produzcan por falta de fondos de acuerdo a las fechas y condiciones de pago del presente contrato, valor que ser&' || 'aacute; detallado en la factura del mes correspondiente. En caso de suspensi&' || 'oacute;n del servicio por falta de pago deber&' || 'aacute; realizar el pago del servicio en uno de los canales de pago correspondientes y comunicarlos a nuestros canales de atenci&' || 'oacute;n al cliente. Adicionalmente el cliente acepta el pago de $3,00 por concepto de reconexi&' || 'oacute;n que ser&' || 'aacute; registrado en la siguiente factura. El tiempo m&' || 'aacute;ximo de reconexi&' || 'oacute;n del servicio despu&' || 'eacute;s del pago es de 24 horas.</li>
                    <li>El cliente acepta que la recepci&' || 'oacute;n las facturas mediante la modalidad de facturaci&' || 'oacute;n electr&' || 'oacute;nica sin costo, o v&' || 'iacute;a f&' || 'iacute;sica acerc&' || 'aacute;ndose a un centro de atenci&' || 'oacute;n de MEGADATOS para solicitar su factura previo el pago de $1,00 por ocasi&' || 'oacute;n por gastos de procesamiento y emisi&' || 'oacute;n de factura. </li>
                    <li>TIPO DE FACTURACI&' || 'Oacute;N:  <div class="box">$!isFactElectronica</div>Electr&' || 'oacute;nica <div class="box"></div>F&' || 'iacute;sica  </li>
                    <li>En caso de tener reclamos debidamente reportados con un ticket y no resueltos por la operadora, puede comunicarse al ARCOTEL a trav&' || 'eacute;s del 1-800-567567 o cir@arcotel.gob.ec</li>
                    <li>Para el pago de los servicios de internet, a nombre de usuarios con discapacidad o de la persona natural o jurdica sin fines de lucro que represente legalmente a la persona con discapacidad, se aplica las rebajas establecidas en la Ley Org&' || 'aacute;nica de Discapacidades vigente y sus futuras reformas, cumpliendo adicionalmente con la resoluci&' || 'oacute;n TEL-072-04-CONATEL-2013 y sus futuras reformas.</li>
                </ul>
            </div><br/><br/><br/>');
    DBMS_LOB.APPEND(bada, '
            <div> <b>CONDICIONES DE OPERACI&' || 'Oacute;N: </b> <br/> </div>
            <br/>
            <div class="clausulas" >
                <ul>
                    <li>El cliente es responsable de mantener una energ&' || 'iacute;a el&' || 'eacute;ctrica regulada de 110V</li>
                    <li>El cliente debe contar con un computador o un dispositivo funcionando adecuadamente con las siguientes caracter&' || 'iacute;sticas m&' || 'iacute;nimas: Procesador pentium III o superior / 512MB de memoria RAM / 20GB m&' || 'iacute;nimo en disco duro / tarjeta de red.</li>
                    <li>Temperatura de operaci&' || 'oacute;n normal de los equipos propiedad de MEGADATOS: 0-30 grados cent&' || 'iacute;grados.</li>
                    <li>Para tener conocimiento sobre las caracter&' || 'iacute;sticas de seguridad que est&' || 'aacute;n impl&' || 'iacute;citas al intercambiar informaci&' || 'oacute;n o utilizar aplicaciones disponibles en la red,favor visite nuestro sitio web: www.netlife.ec</li>
                    <li>Para tener conocimiento de los derechos que lo asisten como usuario, puede encontrar la norma de Calidad de Servicios de Valor Agregado, as&' || 'iacute; como el link directo a la p&' || 'aacute;gina del ARCOTEL en nuestro sitio web: www.netlife.ec</li>
                    <li>Para realizar la medici&' || 'oacute;n del ancho de banda contratado se puede ingresar a la p&' || 'aacute;gina web de NETLIFE www.netlife.ec y utilizar el veloc&' || 'iacute;metro all&' || 'iacute; provisto.</li>
                    <li>El cliente garantizar&' || 'aacute; que el personal designado por MEGADATOS pueda ingresar a los sitios donde se encuentren instalados los equipos parte del presente servicio para realizar trabajos de instalaci&' || 'oacute;n, mantenimiento correctivo o preventivo, revisi&' || 'oacute;n f&' || 'iacute;sica del estado de los equipos propiedad de MEGADATOS y cuando MEGADATOS lo requiera. El incumplimiento de estas condiciones ser&' || 'aacute; causal de terminaci&' || 'oacute;n unilateral de contrato.</li>
                </ul>
            </div>
        </div>
        <br/>
        <!-- ================================================= -->
        <!--    Contrato de prestaci&' || 'oacute;n de servicios     -->
        <!-- ================================================= -->
        <div style="text-align: justify;">
           <span>
                TERCERA.- OBLIGACIONES DEL ABONADO: Las obligaciones del ABONADO son las siguientes: 3.1.- Cancelar a MEGADATOS los valores correspondientes a los servicios contratados en el plan elegido que consta en el anverso de este Contrato o bajo cualquiera de las modalidades aceptadas por la ley de comercio electr&' || 'oacute;nico y en la norma t&' || 'eacute;cnica que regula las condiciones generales de los contratos de adhesi&' || 'oacute;n. 3.2 Obtener la debida autorizaci&' || 'oacute;n y/o licencia del propietario de programas o informaci&' || 'oacute;n en caso de que su transferencia a trav&' || 'eacute;s de las redes nacionales e internacionales de Internet, as&' || 'iacute; lo requieran. 3.3.- Obtener y salvaguardar el uso de la clave de acceso cuando la misma se requiera para la transferencia de informaci&' || 'oacute;n a trav&' || 'eacute;s de las redes nacionales e internacionales de Internet, 3.4.- Respetar y someterse en todo a la Ley Org&' || 'aacute;nica de Telecomunicaciones, Ley de Propiedad Intelectual, y en general a todas las leyes que regulan la materia en el Ecuador. 3.5.- Informarse adecuadamente de las condiciones de cada uno de los servicios que brinda MEGADATOS,los cuales se rigen por el presente Contrato y las leyes aplicables vigentes,no pudiendo alegar desconocimiento de dichas condiciones contractuales. 3.6.- Mantener actualizada la informaci&' || 'oacute;n de contacto,correo,tel&' || 'eacute;fono fijo,tel&' || 'eacute;fono m&' || 'oacute;vil con MEGADATOS para garantizar la recepci&' || 'oacute;n de la informaci&' || 'oacute;n que genera la relaci&' || 'oacute;n contractual.
                </br>
                CUARTA.- OBLIGACIONES DE MEGADATOS: Las obligaciones de MEGADATOS son las siguientes: 4.1.- Suministrar al ABONADO el servicio de acceso a las redes nacionales e internacionales de Internet acatando las disposiciones previstas en la Ley y en el presente Contrato. 4.2.- Actuar con la debida diligencia en la prestaci&' || 'oacute;n del servicio, 4.3.- Respetar y someterse en todo a la Ley Org&' || 'aacute;nica de Telecomunicaciones, Ley Org&' || 'aacute;nica de Defensa del Consumidor,y en general a todas las leyes que en el Ecuador regulan la materia. 4.4.- Implementar los mecanismos necesarios que permitan precautelar la seguridad de sus redes. 4.5.- Entrega o prestar oportuna y eficientemente el servicio,de conformidad a las condiciones establecidas en el contrato y normativa aplicable,sin ninguna variaci&' || 'oacute;n. 4.6.- Notificar cualquier modificaci&' || 'oacute;n de los planes tarifarios al ARCOTEL con al menos 48 horas a su fecha de vigencia seg&' || 'uacute;n lo establecido en la Ley Org&' || 'aacute;nica de Telecomunicaciones.
                <br/>QUINTA.- ALCANCE DE LA RESPONSABILIDAD DE MEGADATOS: Es responsabilidad de MEGADATOS cumplir con las obligaciones contempladas en el presente Contrato. Sin perjuicio de lo anterior se deja expresa constancia que MEGADATOS no se har&' || 'aacute; responsable en los siguientes casos: 5.1.- En caso de que por razones de cambio de tarifas,reformas legales,caso fortuito o fuerza mayor se vea en la obligaci&' || 'oacute;n de suspender el servicio. No obstante lo anterior,MEGADATOS se compromete a informar inmediatamente de este hecho al ABONADO. 5.2.- En caso de que se presente transmisi&' || 'oacute;n de virus a trav&' || 'eacute;s de las redes. 5.3.- El ABONADO recibir&' || 'aacute; los servicios contratados de forma continua, regular, eficiente, con calidad y eficacia, salvo que sea detectado su mal uso, su falta de pago del ABONADO, (aplicar&' || 'aacute; al d&' || 'iacute;a siguiente de cumplida la fecha m&' || 'aacute;xima de pago), por caso fortuito, por uso indebido de los servicios contratados o uso ilegal y en form diferente al paquete contratado, comercializaci&' || 'oacute;n, subarrendamiento, por mandato judicial y por las dem&' || 'aacute;s causas previstas en el ordenamiento jur&' || 'iacute;dico vigente lo cual provocar&' || 'aacute; que MEGADATOS suspenda sus servicios. 5.4.- Por da&' || 'ntilde;os que  llegaran a producirse en los equipos como consecuencia de la utilizaci&' || 'oacute;n de los equipos o del servicio contratado sin contemplar las condiciones de operaci&' || 'oacute;n. 5.5.- En caso de Incumplimiento por parte del ABONADO,de las condiciones contractuales y sus obligaciones establecidas en la Ley Org&' || 'aacute;nica de Defensa del Consumidor y otras leyes aplicables vigentes. EL ABONADO declara que acepta desde ya todas y cada una de las modificaciones que MEGADATOS se vea obligado a efectuar a las condiciones pactadas en el presente Contrato que se deriven de reformas a la normativa al momento de suscripci&' || 'oacute;n del mismo. Tales modificaciones no se entender&' || 'aacute;n como terminaci&' || 'oacute;n anticipada del contrato ni generar&' || 'aacute;n responsabilidad alguna para MEGADATOS. 5.6.- MEGADATOS no podr&' || 'aacute; bloquear,priorizar,restringir o discriminar de modo arbitrario y unilateral aplicaciones,contenidos o servicios sin consentimiento del ABONADO o por orden expresa de la autoridad competente. Del mismo modo podr&' || 'aacute; ofrecer,si el ABONADO lo solicita,servicio de control y bloqueo de contenidos que atenten contra la ley,la moral o las buenas costrumbres,para lo cual informar&' || 'aacute; oportunamente al ABONADO cual es el alcance de la tarifa o precio y modo de funcionamiento de estos. 5.7.- Las condiciones de la prestaci&' || 'oacute;n de los servicios contratados se sujetar&' || 'aacute;n a las leyes,reglamentos,resoluciones,regulaciones,decretos y toda decisi&' || 'oacute;n de car&' || 'aacute;cter general de cualquier instituci&' || 'oacute;n del Estado existente o que se dictaren durante el plazo de ejecuci&' || 'oacute;n del t&' || 'iacute;tulo habilitante que no se encuentren especificadas en la Legislaci&' || 'oacute;n Aplicable.
                <br/>
                SEXTA.- DERECHOS DEL ABONADO: 6.1 Recibir el servicio de acceso a las redes nacionales e internacionales de Internet seg&' || 'uacute;n las disposiciones previstas en la ley y en el presente contrato. 6.2.- Solicitar soporte t&' || 'eacute;cnico seg&' || 'uacute;n las condiciones establecidas en la ley y el presente contrato en caso de ser requerido. 6.3.- Recibir todos los derechos adquiridos seg&' || 'uacute;n la ley org&' || 'aacute;nica de las telecomunicaciones,el reglamento general,el reglamento de prestaci&' || 'oacute;n de servicios de valor agregado y la Ley de defensa del consumidor. 6.4.- Recibir compensaciones por parte del proveedor seg&' || 'uacute;n lo dispuesto por el organismo de control,como notas de cr&' || 'eacute;dito en todos los casos por el servicio no provisto seg&' || 'uacute;n las condiciones contractuales. 6.5.- Los nuevos derechos y beneficios para el ABONADO que se establezcan a futuro se incorporar&' || 'aacute;n de manera autom&' || 'aacute;tica al presente contrato por disposici&' || 'oacute;n del ARCOTEL.
                <br/>SEPTIMA.- PRECIO Y FORMA DE PAGO: El precio de los servicios contratados por EL ABONADO y los impuestos constan descritos en el anverso de este Contrato, el cual puede ser cancelado en dinero en efectivo, dep&' || 'oacute;sito, transferencia mediante bot&' || 'oacute;n de pago, d&' || 'eacute;bito, tarjeta de cr&' || 'eacute;dito u otras que implemente o facilite MEGADATOS, de acuerdo a los t&' || 'eacute;rminos de contrataci&' || 'oacute;n. En caso de que EL ABONADO incurra en mora de uno o m&' || 'aacute;s pagos,MEGADATOS se reserva el derecho de suspender el servicio y dar por terminado el mismo sin notificaci&' || 'oacute;n o requerimiento alguno; sin perjuicio de las acciones legales que el incumplimiento de esta obligaci&' || 'oacute;n diera lugar. En caso de mora MEGADATOS aplicar&' || 'aacute; la m&' || 'aacute;xima tasa de inter&' || 'eacute;s permitida por la ley por el periodo en mora. Para el caso de que se contrate servicios adicionales y suplementarios con costo,el ABONADO se compromete a firmar una adenda verbal grabada,electr&' || 'oacute;nica con firma digital o f&' || 'iacute;sica al presente contrato,de igual manera cuando se desuscriba de los mismos.
                <br/>
                OCTAVA.- PRIVACIDAD Y TRATAMIENTO DE INFORMACI&' || 'Oacute;N: MEGADATOS garantizar&' || 'aacute; la privacidad y confidencialidad de la informaci&' || 'oacute;n del ABONADO y s&' || 'oacute;lo la utilizar&' || 'aacute; para brindar el servicio contratado por el ABONADO,por lo que el ABONADO conoce y
                <div id="col" class="col-width-10">
                    <div class="box">$!isSiAutoriza</div>
                    <div class="box-label">Si</div>
                </div>
                <div id="col" class="col-width-10">
                    <div class="box">$!isNoAutoriza</div>
                    <div class="box-label">No</div>
                </div>
                 autoriza que MEGADATOS pueda proporcionar a terceros datos necesarios para poder realizar la entrega de estado de cuenta,facturaci&' || 'oacute;n,recordatorios de fechas de pago o montos de pago,fidelizaci&' || 'oacute;n,informaci&' || 'oacute;n de nuevos servicios,informaci&' || 'oacute;n de promociones especiales,entre otros; as&' || 'iacute; mismo tambi&' || 'eacute;n autoriza a hacer uso de esta informaci&' || 'oacute;n para fines comerciales o de brindar beneficios al ABONADO a trav&' || 'eacute;s de alianzas desarrolladas. Adicionalmente EL ABONADO acepta expresamente que MEGADATOS puede utilizar medios electr&' || 'oacute;nicos y llamadas para: 8.1.- Notificar cambios relacionados con los t&' || 'eacute;rminos y condiciones del presente CONTRATO, 8.2.- Realizar gestiones de cobtranzas y dem&' || 'aacute;s promociones aplicables de acuerdo a la normativa vigente. Sin embargo de lo anterior,MEGADATOS podr&' || 'aacute; entregar los datos del ABONADO en caso de requerimientos realizados por autoridad competente conforme al ordenamiento jur&' || 'iacute;dico vigente y particularmente de la Agencia de Regulaci&' || 'oacute;n y Control de las Telecomunciaciones para el cumplimiento de sus funciones.
                <br/>
                NOVENA.- FACTURACI&' || 'Oacute;N: MEGADATOS facturar&' || 'aacute; y cobrar&' || 'aacute; al ABONADO el servicio contratado en forma mensual basado en el ciclo de facturaci&' || 'oacute;n en que haya sido definido.  Para ejecutar cancelaciones de servicio o downgrades,el ABONADO deber&' || 'aacute; notificar con 15 d&' || 'iacute;as de anticipaci&' || 'oacute;n a la fecha de finalizaci&' || 'oacute;n de su ciclo de facturacion. El primer pago constar&' || 'aacute; del valor de instalaci&' || 'oacute;n y el valor proporcional del primer per&' || 'iacute;odo de consumo correspondiente. MEGADATOS entregar&' || 'aacute; a sus ABONADOs las facturas de conformidad con la ley,sin embargo la no recepci&' || 'oacute;n de dicho documento no exime al ABONADO del pago correspondiente. El ABONADO cancelar&' || 'aacute; por periodos mensuales a MEGADATOS por la prestaci&' || 'oacute;n del servicio contratado a los precios pactados a trav&' || 'eacute;s de &' || 'eacute;ste instrumento y sus anexo(servicios adicionales),hasta el fin del per&' || 'iacute;odo; si el ABONADO no cancelare los valores facturados dentro del plazo previsto,MEGADATOS suspender&' || 'aacute; de forma autom&' || 'aacute;tica los servicios en cualquier momento a partir del vencimiento de dicho plazo. El ABONADO podr&' || 'aacute; pedir la reactivaci&' || 'oacute;n del servicio en un m&' || 'aacute;ximo de 30 d&' || 'iacute;as posteriores a la suspensi&' || 'oacute;n,previo al pago de los valores adeudados,caso contrario el servicio ser&' || 'aacute; dado por cancelado. El tiempo de reactivaci&' || 'oacute;n del servicio es de 24 horas despu&' || 'eacute;s de que el ABONADO haya pagado los valores pendientes y haya hecho el pedido de reactivaci&' || 'oacute;n.
                <br/>DECIMA.- VIGENCIA: El plazo de duraci&' || 'oacute;n del presente Contrato es de 36 meses y tendr&' || 'aacute; vigencia desde la fecha de instalaci&' || 'oacute;n y activaci&' || 'oacute;n del servicio que se indicar&' || 'aacute; en la facturaci&' || 'oacute;n mensual, en el cual
                <div id="col" class="col-width-10">
                    <div class="box">$!isSiRenueva</div>
                    <div class="box-label">Si</div>
                </div>
                <div id="col" class="col-width-10">
                    <div class="box">$!isNoRenueva</div>
                    <div class="box-label">No</div>
                </div>

                se renovar&' || 'aacute; autom&' || 'aacute;ticamente en per&' || 'iacute;odos iguales y sucesivos, mientras las partes no soliciten una terminaci&' || 'oacute;n del mismo, se podr&' || 'aacute; realizar una revisi&' || 'oacute;n periodica de tarifas en funci&' || 'oacute;n de condiciones de mercado y de mutuo acuerdo. El operador respetar&' || 'aacute; las condiciones establecidas en la ley org&' || 'aacute;nica de defensa del consumidor para la prestaci&' || 'oacute;n de los servicios entre las partes.
                <br/>
                D&' || 'Eacute;CIMO PRIMERA.- TERMINACI&' || 'Oacute;N DEL CONTRATO: Para el caso de terminaci&' || 'oacute;n del contrato, el ABONADO se compromete a cancelar los valores adeudados a MEGADATOS y a entregar los equipos de propiedad de MEGADATOS en las oficinas de MEGADATOS habilitados para este prop&' || 'oacute;sito que se indican en la secci&' || 'oacute;n de atenci&' || 'oacute;n al cliente de la p&' || 'aacute;gina web www.netlife.ec, en perfectas condiciones, salvo por deterioros normales causados por el uso diligente. Sin perjuicio de lo anterior, son causales de terminaci&' || 'oacute;n anticipada del presente instrumento,las siguientes: 11.1.- Aplicaci&' || 'oacute;n de las normas legales,el caso fortuito o fuerza mayor que obliguen a MEGADATOS a suspender definitivamente el servicio. 11.2.- La suspensi&' || 'oacute;n definitiva de servicios prestados por los proveedores de MEGADATOS. 11.3.- Incumplimiento de Ias obligaciones contractuales de las partes,no pago del servicio o mal uso del servicio derivadas del presente Contrato,incluyendo la manipulaci&' || 'oacute;n o retiro de equipos provistos por MEGADATOS,y todas las mecionadas en el presente contrato en el literal condiciones. 11.4.- En caso de que el servicio se est&' || 'eacute; utilizando en un Cyber o ISP,bastando para la terminaci&' || 'oacute;n un informe/reporte generado por MEGADATOS que confirme esto,sin eximir del pago de todos los valores que se adeuden,entregar los equipos que proveen del servicio o su valor en efectivo y cumplir las condiciones de permanencia m&' || 'iacute;nima. 11.5.- Por acuerdo mutuo. 11.6.- Por decisi&' || 'oacute;n unilateral de acuerdo a la ley de defensa del consumidor,sin que hayan multas o recargos para ello.
                Para el caso puntual de promociones, el ABONADO
                <div id="col" class="col-width-10">
                    <div class="box">$!isSiAcceder</div>
                    <div class="box-label">Si</div>
                </div>
                <div id="col" class="col-width-10">
                    <div class="box">$!isNoAcceder</div>
                    <div class="box-label">No</div>
                </div>
                desea acceder a las promociones que consideran un plazo m&' || 'iacute;nimo de permanencia es de 36 meses para hacerlas efectivas y permanecer vigentes y acceder a los promocionales de MEGADATOS, en tal virtud, en caso de una terminaci&' || 'oacute;n anticipada del contrato, el ABONADO dejar&' || 'aacute; de beneficiarse de dicho descuento, promoci&' || 'oacute;n o costo de instalaci&' || 'oacute;n,y por lo tanto se le aplicar&' || 'aacute;n las tarifas regulares por los servicios e instalaci&' || 'oacute;n contratados prorrateados en funci&' || 'oacute;n del tiempo de permanencia. Para tal efecto en la &' || 'uacute;ltima factura emitida al ABONADO, se reflejar&' || 'aacute; la respectiva reliquidaci&' || 'oacute;n de valores del servicio contratado en base al valor real del mismo.
                <br/>
                D&' || 'Eacute;CIMO SEGUNDA.- DECLARACI&' || 'Oacute;N FUNDAMENTAL: El ABONADO declara que ha obtenido de forma oportuna por parte de MEGADATOS,toda la informaci&' || 'oacute;n veraz y completa del servicio contratado. As&' || 'iacute; mismo declara que conoce &' || 'iacute;ntegramente el presente contrato en su anverso y reverso y que lo acepta en todas sus partes por convenir a sus intereses.
                <br/>
                D&' || 'eacute;CIMA TERCERA.- CESI&' || 'oacute;N: EL ABONADO acepta desde ya cualquier cesi&' || 'oacute;n parcial o total que realice MEGADATOS de los derechos y/u obligaciones contenidos en este Contrato. El ABONADO puede ceder el presente contrato previo a realizar el tr&' || 'aacute;mite correspondiente de cesi&' || 'oacute;n de derechos en los canales de atenci&' || 'oacute;n al ABONADO de NETLIFE.
                <br/>D&' || 'eacute;CIMO CUARTA.- ACUERDO TOTAL: El presente Contrato Contiene los acuerdos totales de las partes y deja sin efecto cualquier negociaci&' || 'oacute;n,entendimiento,contrato o convenio que haya existido previamente entre el ABONADO y MEGADATOS,el presente instrumento incluye todas las condiciones a las que se compromete la empresa y el alcance &' || 'uacute;nico de sus servicios y deja sin efecto cualquier informaci&' || 'oacute;n adicional recibida que no conste en el mismo. Si el ABONADO desea contratar servicios adicionales, &' || 'eacute;stos ser&' || 'aacute;n agregados al presente contrato.
                <br/>
                D&' || 'Eacute;CIMO QUINTA.- CONTROVERSIAS: Las controversias o diferencias que surjan entre las partes con ocasi&' || 'oacute;n de la firma, ejecuci&' || 'oacute;n, interpretaci&' || 'oacute;n, pr&' || 'oacute;rroga o terminaci&' || 'oacute;n del Contrato, as&' || 'iacute; como de cualquier otro asunto relacionado con el presente Contrato, ser&' || 'aacute;n sometidas a la revisi&' || 'oacute;n de las partes para buscar un arreglo directo, en t&' || 'eacute;rmino no mayor a CINCO(5) d&' || 'iacute;as h&' || 'aacute;biles a partir de la fecha en que cualquiera de las partes comunique por escrito a la otra parte la existencia de una diferencia y la explique someramente. Si no se resolviere de esta manera, tratar&' || 'aacute;n de solucionarlo con la asistencia de un mediador de la C&' || 'aacute;mara de Comercio de Quito; en caso de que no pueda ser solucionada en mediaci&' || 'oacute;n  las partes
                <div id="col" class="col-width-10">
                    <div class="box">$!isSiMediacion</div>
                    <div class="box-label">Si</div>
                </div>
                <div id="col" class="col-width-10">
                    <div class="box">$!isNoMediacion</div>
                    <div class="box-label">No</div>
                </div>
                , seg&' || 'uacute;n sus intereses podr&' || 'aacute;n someterse a la Justicia Ordinaria y/o a trav&' || 'eacute;s de un Tribunal de Arbitraje de la C&' || 'aacute;mara de Comercio de Quito, el mismo que se sujetar&' || 'aacute; a lo dispuesto en la Ley de Arbitraje y Mediaci&' || 'oacute;n, y dem&' || 'aacute;s normativas y preceptos.

                <br/>
                D&' || 'Eacute;CIMO SEXTA.- NOTIFICACIONES: Toda y cualquier notificaci&' || 'oacute;n que requiera realizarse en relaci&' || 'oacute;n con el presente Contrato,se har&' || 'aacute; por escrito a las siguientes direcciones: Uno.- MEGADATOS:  Quito: Av. N&' || 'uacute;&' || 'ntilde;ez de Vela E3-13 y Atahualpa,Edificio torre del Puente Piso 2,Guayaquil: Av. Rodrigo de Chavez,Parque Empresarial Col&' || 'oacute;n Ed.Coloncorp,Torre 6,Locales 4 y 5,&' || 'oacute; en la direcci&' || 'oacute;n de correo electr&' || 'oacute;nico info@netlife.ec</span> 14.2.- ABONADO en la direcci&' || 'oacute;n indicada en el anverso del presente contrato o en su direcci&' || 'oacute;n de correo electr&' || 'oacute;nico.
                De presentarse cambios en las direcciones enunciadas,la parte respectiva dar&' || 'aacute; aviso escrito de tal hecho a la otra,dentro de las 24 horas de producido el cambio. Para constancia de todo lo expuesto y convenido,las partes suscriben el presente contrato,en la ciudad y fecha indicada en el anverso del presente contrato,en tres ejemplares de igual tasa y valor.
                </div>');
    DBMS_LOB.APPEND(bada, '

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
                            <div id="colCell" class="col-width-50">MEGADATOS</div>
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
                            <div id="colCell" class="col-width-50">Firma del Cliente</div>
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
                            <li>Copia de C&' || 'eacute;dula de Identidad o pasaporte</li>
                            <li>Copia de encabezado de estado de cuenta (Corriente/Ahorro/TC) en caso de hacer d&' || 'eacute;bito autom&' || 'aacute;tico.</li>
                            <li>Copia de la calificaci&' || 'oacute;n de discapacidad emitida por el CONADIS, que determine el tipo y porcentaje de discapacidad igual o mayor al 30%. (Si aplica)</li>
                            <li>En caso de discapacidad, factura original de un servicio b&' || 'aacute;sico que demuestre la residencia del solicitante para acceder al servicio.</li>
                        </ul>
                    </div>
                </div>
                <div id="colCell" class="col-width-5"></div>
                <div id="colCell" class="col-width-35">
                    <div> <b>Personas Jur&' || 'iacute;dicas: </b> </div>
                    <div class="clausulas" >
                        <ul>
                            <li>Copia del RUC</li>
                            <li>Copia de encabezado de estado de cuenta (Corriente/Ahorro/TC) en caso de hacer d&' || 'eacute;bito autom&' || 'aacute;tico.</li>
                            <li>Copia de c&' || 'eacute;dula o pasaporte de representante legal.</li>
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
        <div class="labelBlock">FORMA DE PAGO E INFORMACI&' || 'Oacute;N DE CR&' || 'Eacute;DITO</div>
        <div id="contenedor" class="col-width-100">
            <div id="row">
                <div id="col" class="col-width-5"></div>
                <div id="col" class="col-width-25">
                    <div class="box-label">Tarjeta de Cr&' || 'eacute;dito</div>
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
            <span>El cliente declara haber le&' || 'iacute;do este contrato y la solicitud de prestaci&' || 'oacute;n de servicios en su totalidad y declara que esta conforme con todas y cada una de sus cl&' || 'aacute;usulas. El cliente declara que la informaci&' || 'oacute;n suministrada a MEGADATOS es ver&' || 'aacute;z y correcta. Adicionalmente autoriza a MEGADATOS a verificarla. El cliente autoriza a MEGADATOS expresamente a entregar y requerir informaci&' || 'oacute;n,en forma directa,a los buros de informaci&' || 'oacute;n crediticia o entidades designadas para estas calificaciones sobre su comportamiento y capacidad de pago,su desempe&' || 'ntilde;o como deudor,para valorar su riesgo futuro.
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
                            <div id="colCell" class="col-width-50">Firma del Cliente</div>
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


UPDATE DB_FIRMAELECT.ADM_EMPRESA_PLANTILLA
SET HTML= bada
where COD_PLANTILLA = 'contratoMegadatos';

UPDATE DB_FIRMAELECT.ADM_EMP_PLANT_CERT
   SET PROPIEDADES = '{
	"llx": "90",
	"lly": "670",
	"urx": "240",
	"ury": "700",
	"pagina": "4",
	"textSignature": "",
	"modoPresentacion": "1"
}'
where CODIGO = 'FIRMA_CONT_MD_FORMA_PAGO'; --Firma cliente forma de pago contrato megadatos

UPDATE DB_FIRMAELECT.ADM_EMP_PLANT_CERT
   SET PROPIEDADES = '{
	"llx": "380",
	"lly": "170",
	"urx": "550",
	"ury": "200",
	"pagina": "3",
	"textSignature": "",
	"modoPresentacion": "1"
}'
where CODIGO = 'FIRMA_CONT_MD_FINAL_CLIENTE'; --Firma contrato MD cliente

UPDATE DB_FIRMAELECT.ADM_EMP_PLANT_CERT
   SET PROPIEDADES = '{
	"llx": "120",
	"lly": "170",
	"urx": "290",
	"ury": "200",
	"pagina": "3",
	"textSignature": "",
	"modoPresentacion": "1"
}'
where CODIGO = 'FIRMA_CONT_MD_FINAL_EMPRESA'; --Firma contrato MD empresa


COMMIT;
end;

/