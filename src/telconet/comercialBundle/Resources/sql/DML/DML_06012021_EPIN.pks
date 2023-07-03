SET DEFINE OFF;
UPDATE DB_FIRMAELECT.ADM_EMPRESA_PLANTILLA
SET HTML=EMPTY_CLOB()
where COD_PLANTILLA = 'adendumMegaDatos';
commit;

SET SERVEROUTPUT ON;
declare
    bada clob:='<!DOCTYPE html>';
begin

--DBMS_LOB.FREETEMPORARY(bada);

--dbms_lob.createtemporary(bada, TRUE);
DBMS_LOB.APPEND(bada, '<!DOCTYPE html> 
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
    <tr style="$!verLeyenda">
        <td class="col-width-75" style="font-size:14px;">FE DE ERRATAS: este adendum es emitido como correcci&oacute;n al adendum del contrato $!numeroContrato</td>
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
        </div><div style="clear: both;"><br /></div>
        <div class="col-width-100" style="text-align: justify;">
');
                                    
DBMS_LOB.APPEND(bada, '        <div>NETLIFE puede modificar estas condiciones o las condiciones adicionales que se apliquen a un Servicio o Producto con el fin, por ejemplo, de reflejar cambios legislativos, sustituci&'||'oacute;n y/o mejoras en los Servicios prestados. La contrataci&'||'oacute;n de nuestros Servicios implica la aceptaci&'||'oacute;n de las condiciones descritas a este documento por lo que el cliente entiende y acepta que las ha leído detenidamente y conoce todos sus detalles.</div>
    
            <br />
            <div> <b>INFORMACION Y CONDICIONES ADICIONALES: </b> </div>
            <br />
            <div class="clausulas">
                <ul>
                    <li>El cliente conoce, acepta las condiciones aqu&'||'iacute; descritas, por lo cu&'||'aacute;l suscribe el presente documento de contrataci&'||'oacute;n de servicios adicionales, el cual forma parte del contrato de adhesión bajo la misma forma de pago suscrita entre el cliente y NETLIFE.</li>
                    <br />
                    <li>El cliente conoce, entiende y acepta que ha recibido toda la informaci&'||'oacute;n referente al servicio(s) /producto(s) adicional(es) contratado(s)  y que est&'||'aacute; de acuerdo con todos los items descritos en el presente documento. El cliente conoce, entiende y acepta que el servicio contratado con NETLIFE NO incluye cableado interno o configuraci&'||'oacute;n de la red local del cliente e incluye condiciones de permanencia m&'||'iacute;nima en caso de recibir promociones.</li>
                    <br />
                    <li>Los servicios adicionales generan una facturación proporcional al momento de la contratación y luego ser&'||'aacute; de forma recurrente. El servicio adicional estar&'||'aacute; activo mientras el cliente est&'||'eacute; al d&'||'iacute;a en pagos, caso contrario no podr&'||'aacute; acceder al mismo por estar suspendido.</li>  
                </ul>
            </div>

            <br /><br />
            <div><b>NETLIFE CLOUD (MICROSOFT 365 FAMILIA):</b> </div>
            <br />
            <div class="clausulas">
                <ul>
                    <li>El cliente conoce que debe contar con una cuenta microsoft (Cuentas @hotmail.com, @outlook.com, etc ) activa para utilizar este producto.</li>
                    <br />
                    <li>El producto tiene una vigencia de 12 meses e incluye renovaci&'||'oacute;n autom&'||'aacute;tica de licencia. En caso de cancelarlo antes de los 12 meses de cualquiera de sus periodos de vigencia y renovaci&'||'oacute;n, el cliente entiende, acepta y suscribe que sea facturado el valor proporcional, de acuerdo al tiempo de vigencia que resta por cubrir.</li>
                    <br />
                    <li>Este producto se puede instalar en PCs y tabletas Windows que ejecuten Windows 7 o una versi&'||'oacute;n posterior, y equipos Mac con Mac OS X 10.6 o una versi&'||'oacute;n posterior.  Microsoft 365 para iPad se puede instalar en iPads que ejecuten la &'||'uacute;ltima versión de iOS. Microsoft Mobile para iPhone se puede instalar en tel&'||'eacute;fonos que ejecuten iOS 6.0 o una versi&'||'oacute;n posterior. Microsoft  Mobile para tel&'||'eacute;fonos Android se puede instalar en tel&'||'eacute;fonos que ejecuten OS 4.0 o una versi&'||'oacute;n posterior. Para obtener más informaci&'||'oacute;n sobre los dispositivos y requerimientos, visite: www.office.com/information.</b>.</li>
                    <br />
                    <li>La entrega de este producto no incluye el servicio de instalaci&'||'oacute;n del mismo en ning&'||'uacute;n dispositivo. Para tal efecto, dentro del producto se encuentra una gu&'||'iacute;a de instalaci&'||'oacute;n a seguir por el cliente. El cliente es responsable de la instalaci&'||'oacute;n y configuraci&'||'oacute;n del producto en sus dispositivos y usuarios.</li>
                    <br />
                    <li>El canal de soporte para consultas, dudas o requerimientos espec&'||'iacute;ficos del producto Microsoft 365 Familia podr&'||'aacute; ser realizado a trav&'||'eacute;s del tel&'||'eacute;fono: 1-800-010-288.</li>
                    <br />
                    <li>Los pasos para instalar y empezar a utilizar Microsoft 365 Familia se encuentran en el siguiente link: office.com/setup. Para administrar los dispositivos y cuentas de su licencia Microsoft 365 Familia el cliente puede acceder al link: office.com/myaccount.</li>
                </ul>
            </div>

            <br /><br />
            <div><b>RENTA DE EQUIPOS: WIFI Dual Band Premium y/o AP Extender Dual Band:</b> </div>
            <br />
            <div class="clausulas">
                <ul>
                    <li>El equipo es propiedad de MEGADATOS S.A. y cuenta con una garant&'||'iacute;a de 1(UN) año por defectos de f&'||'aacute;brica. Al finalizar la prestaci&'||'oacute;n del servicio el cliente deber&'||'aacute; entregarlo en las oficinas de MEGADATOS. En caso de que el cliente no lo devolviere, se detecte mal uso o daños, el costo total del equipo por reposici&'||'oacute;n ser&'||'aacute; facturado al cliente. En caso del router WiFi Dual Band Premium es de $175,00 (más IVA) y del AP Extender Dual Band es de $75.00 (más IVA).</li>
                    <br />
                    <li>El cliente conoce y acepta que, para garantizar la calidad del servicio, estos equipos ser&'||'aacute;n administrado por NETLIFE mientras dure la prestaci&'||'oacute;n del servicio.</li>
                    <br />
                    <li>El equipo WiFi provisto por NETLIFE tiene puertos al&'||'aacute;mbricos que permiten la utilizaci&'||'oacute;n &'||'oacute;ptima de la velocidad ofertada en el plan contratado, adem&'||'aacute;s cuenta con conexi&'||'oacute;n WiFi a una frecuencia de 5Ghz que permite una velocidad m&'||'aacute;xima de 150Mbps a una distancia de 3 metros y pueden conectarse equipos a una distancia de hasta 12 metros en condiciones normales, sin embargo, la distancia de cobertura var&'||'iacute;a seg&'||'uacute;n la cantidad y tipo de paredes, obst&'||'aacute;culos e interferencia que se encuentren en el entorno. El cliente conoce y acepta que la tecnolog&'||'iacute;a WiFi pierde potencia a mayor distancia y por lo tanto se reducir&'||'aacute; la velocidad efectiva a una mayor distancia de conexi&'||'oacute;n del equipo.</li>
                </ul>
            </div>

            <br /><br />
            <div><b>NETLIFE DEFENSE:</b> </div>
            <br />
            <div class="clausulas">
                <ul>
                    <li>Netlife Defense es un servicio de seguridad inform&'||'aacute;tica que permite reducir los riesgos de vulnerabilidades en la navegaci&'||'oacute;n y transacciones por internet.</li>
                    <br />
                    <li>El m&'||'eacute;todo de entrega de este servicio es mediante env&'||'iacute;o de correo electr&'||'oacute;nico al correo registrado por el cliente en su contrato o en esta solicitud. Este correo debe ser un correo electr&'||'oacute;nico v&'||'aacute;lido. Es responsabilidad del cliente verificar que el correo no se encuentre alojado en la carpeta de correo no deseado. En caso de requerirlo, el cliente podr&'||'aacute; solicitar el reenvio de &'||'eacute;ste.</li>
                    <br />
                    <li>Para que esta soluci&'||'oacute;n de seguridad inform&'||'aacute;tica est&'||'eacute; en operaci&'||'oacute;n, es necesaria la instalaci&'||'oacute;n del software en el dispositivo que requiera protegerse.</li>
                    <br />
                    <li>Netlife Defense soporta: Equipos de escritorio y port&'||'aacute;tiles: Windows 10/8.1 /8 /7 o superior; OS X 10.12 – macOS 10.13 o superiores; Tablets: Windows 10 / 8& 8.1 / Pro (64 bits); iOS 9.0 o posterior; Smartphones: Android 4.1 o posterior, iOS 9.0 o posterior (solo para navegaci&'||'oacute;n; a través, de Kaspersky Safe Browser).</li>
                    <br />
                    <li>Requerimientos m&'||'iacute;nimos del sistema: Disco Duro: Windows: 1.500 MB; Mac 1220 MB. Memoria (RAM) libre:1 GB (32 bits) o 2 GB (64 bits). Resoluci&'||'oacute;n m&'||'iacute;nima de pantalla 1024x600 (para tablets con Windows), 320x480 (para dispositivos Android). Conexi&'||'oacute;n Activa a Internet.</li>
                </ul>
            </div>

            <br /><br />
            <div><b>NETLIFE ASSISTANCE:</b> </div>
            <br />
            <div class="clausulas">
                <ul>
                    <li>NetlifeAssistance es un servicio que brinda soluciones remotas ilimitadas de asistencia t&'||'eacute;cnica para equipos terminales del cliente, entre los cuales est&'||'aacute;n: Asistencia guiada de configuraci&'||'oacute;n e instalaci&'||'oacute;n de software o hardware; Revisi&'||'oacute;n, an&'||'aacute;lisis y mantenimiento del PC/MAC; Asesor&'||'iacute;a t&'||'eacute;cnica en l&'||'iacute;nea las 24 horas  del PC/MAC; T&'||'eacute;cnico PC y dispositivos remoto ilimitado; Hasta 3 visitas presenciales al año; un traslado/reubicaci&'||'oacute;n al año; valor normal: $8.75+iva mensual.</li>
                    <br />
                    <li>El servicio no incluye materiales, sin embargo si el cliente los requiere se cobrar&'||'aacute;n por separado. Tampoco incluye reparaci&'||'oacute;n de equipos o dispositivos.</li>
                    <br />
                    <li>El servicio tiene un tiempo de permanencia m&'||'iacute;nima de 12 meses. En caso de cancelaci&'||'oacute;n anticipada aplica la clausula de pago de los descuentos a los que haya accedido por promociones, tales como Instalaci&'||'oacute;n, tarifas preferenciales, etc.</li>
                    <br />
                    <li>El servicio aplica para planes hogar de las ciudades de Quito y Guayaquil.</li>
                </ul>
            </div>

            <br /><br />
            <div><b>NETLIFE ASSISTANCE PRO:</b> </div>
            <br />
            <div class="clausulas">
                <ul>
                    <li>Netlife Assistance PRO es un servicio que brinda soluciones a los problemas t&'||'eacute;cnicos e inform&'||'aacute;ticos de un negocio para mejorar su operaci&'||'oacute;n, este servicio incluye: Asistencia guiada de configuraci&'||'oacute;n, sincronizaci&'||'oacute;n y conexi&'||'oacute;n a red de software o hardware: PC, MAC; Revisi&'||'oacute;n, an&'||'aacute;lisis y mantenimiento del PC/MAC/LINUX/SmartTV/Smartphones/Tablets/Apple TV/Roku, etc.; Asesor&'||'iacute;a t&'||'eacute;cnica en l&'||'iacute;nea las 24 horas v&'||'iacute;a telef&'||'oacute;nica o web por store.netlife.net.ec; Un servicio de Help Desk con ingenieros especialistas.</li>
                    <br />
                    <li>No incluye capacitaci&'||'oacute;n en el uso del Sistema Operativo y software, &'||'uacute;nicamente se solucionar&'||'aacute;n incidencias puntuales.</li>
                    <br />
                    <li>El servicio tiene un tiempo de permanencia m&'||'iacute;nima de 12 meses. En caso de cancelaci&'||'oacute;n anticipada aplica la clausula de pago de los descuentos a los que haya accedido por promociones, tales como Instalaci&'||'oacute;n, tarifas preferenciales, etc.</li>
                    <br />
                    <li>Se puede ayudar a reinstalar el Sistema Operativo del dispositivo del cliente, siempre y cuando se disponga de las licencias y medios de instalaci&'||'oacute;n originales correspondientes.</li>
                    <br />
                    <li>Sistemas Operativos sobre los cuales se brinda soporte a incidencias: Windows: XP hasta 10, Windows Server: 2003 hasta 2019, MacOs: 10.6 (Snow Leopard) hasta 10.14 (Mojave), Linux: Ubuntu 19.04, Fedora 30, Open SUSE 15.1, Debian 10.0, Red Hat 8, CentOS 7, iOS: 7.1.2 a 12.3.2, Android: Ice Cream Sandwich 4.0 hasta Pie 9.0, Windows Phone OS: 8.0 hasta 10 Mobile.</li>
                    <br />
                    <li>Asistencia Hardware: Los controladores o software necesarios para el funcionamiento del hardware son responsabilidad del usuario, aunque prestaremos todo nuestro apoyo para obtenerlos en caso necesario.  Asistencia Software: No incluye capacitaci&'||'oacute;n en el uso del Software. Las licencias y medios de instalaci&'||'oacute;n son a cargo del usuario. Nunca se prestar&'||'aacute; ayuda sobre software ilegal.</li>
                    <br />
                    <li>El 100% de las conversaciones chat levantadas v&'||'iacute;a web; a trav&'||'eacute;s de, Netlife Access (store.netlife.net.ec) se mantendr&'||'aacute;n registradas en la plataforma durante 60 d&'||'iacute;as m&'||'aacute;ximo</li>
                </ul>
            </div>

            <br /><br />
            <div><b>CONSTRUCTOR WEB:</b> </div>
            <br />
            <div class="clausulas">
                <ul>
                    <li>Constructor Web es un servicio que te permite construir tu propia página web, tener 1 dominio propio y 5 cuentas de correo asociadas a este dominio. Adem&'||'aacute;s de, asesor&'||'iacute;a t&'||'eacute;cnica en l&'||'iacute;nea las 24 horas v&'||'iacute;a telef&'||'oacute;nica o web por store.netlife.net.ec . La propiedad del dominio está condicionada a un tiempo de permanencia m&'||'iacute;nima de 12 meses y se renueva anualmente. En caso de cancelarlo antes de los 12 meses de cualquiera de sus per&'||'iacute;odos de vigencia y renovaci&'||'oacute;n, el cliente entiende y acepta que sea facturado el valor proporcional, de acuerdo con el tiempo de vigencia que resta por cubrir. Es responsabilidad del cliente tomar las medidas necesarias para almacenar la informaci&'||'oacute;n colocada en su p&'||'aacute;gina web.</li>
                    <br />
                    <li>Se incluye el servicio de diseño de la página web por parte del equipo de diseño bajo solicitud del usuario y sujeto al env&'||'iacute;o de la informaci&'||'oacute;n relevante para su creaci&'||'oacute;n. El servicio incluye hasta 5 páginas de contenido, formulario de contacto para recibir comunicaci&'||'oacute;n de los visitantes a un correo especificado, links a las redes sociales, mapa de Google interactivo, conexi&'||'oacute;n con Google Analytics. El tiempo de entrega/publicaci&'||'oacute;n estimado es de 5 d&'||'iacute;as h&'||'aacute;biles, pero est&'||'aacute; sujeto al env&'||'iacute;o oportuno de informaci&'||'oacute;n del cliente, as&'||'iacute; como del volumen de material recibido.</li>
                    <br />
                    <li>Webmail: Administraci&'||'oacute;n de correos, carpetas, y filtros con una interfaz intuitiva y f&'||'aacute;cil de utilizar proporcionada por Roundcube. Se puede agregar cualquier cuenta IMAP/POP para tener una &'||'uacute;nica interfaz.</li>
                    <br />
                    <li>Navegadores Soportados: Windows Vista, 7, y 8 | IE 9.0 en adelante  | Firefox versión 19 en adelante. | Google Chrome versión 25 en adelante  | Windows 10 | Edge 12 en adelante | Mac OS X 10.4, 10.5, y 10.6 | Firefox versión 19 en adelante | Safari versión 4.0 en adelante.</li>
                    <br />
                    <li>Se considera “spam” la pr&'||'aacute;ctica de enviar mensajes de correo electr&'||'oacute;nico no deseados, a menudo con contenido comercial, en grandes cantidades a los usuarios, sin darles la opci&'||'oacute;n de darse de baja o excluirse de una lista de distribuci&'||'oacute;n. Por lo anterior, queda prohibido que el cliente use el correo para estos fines. En caso de cualquier violaci&'||'oacute;n a estas Pol&'||'iacute;ticas, se proceder&'||'aacute; a tomar una de las siguientes medidas: 1ro Suspender/Bloquear la cuenta por un lapso de 72 horas. -  2do Suspender/Bloquear la cuenta por un lapso de 144 horas. - 3ro Suspender/Bloquear todo tr&'||'aacute;fico del dominio y se iniciar&'||'aacute; el proceso de baja de servicio.</li>
                    <br />
                    <li>El acceso al servicio es posible desde Netlife Access (store.netlife.net.ec)</li>
                </ul>
            </div>
       
');
                                    
DBMS_LOB.APPEND(bada, '
            <br /><br />
            <div><b>PUNTO CABLEADO ETHERNET:</b> </div>
            <br />
            <div class="clausulas">
                <ul>
                    <li>Punto Cableado Ethernet es un producto que contempla la instalaci&'||'oacute;n o acondicionamiento de un (1) punto cableado a un (1) dispositivo del cliente, para acceso a internet directo por cable. El producto tiene un metraje m&'||'aacute;ximo de 30mts e incluye para su acondicionamiento 2 conectores (cat6) y 10 metros de canaleta. </li>
                    <br />
                    <li>Por la contrataci&'||'oacute;n del producto el cliente realiza un pago &'||'uacute;nico de $35,00+iva, que se incluir&'||'aacute; en su siguiente factura. Este producto no tiene un tiempo m&'||'iacute;nimo de permanencia y no es sujeto de traslado.</li>
                    <br />
                    <li>La contrataci&'||'oacute;n del servicio est&'||'aacute; limitada a 3 puntos cableados por punto del cliente.</li>
                    <br />
                    <li>En caso de que el cliente requiera que se le retire el punto cableado, se le cobrar&'||'aacute; el valor de la visita t&'||'eacute;cnica  programada cuyo valor se puede encontrar en la secci&'||'oacute;n de atenci&'||'oacute;n al cliente de: https://www.netlife.ec</li>
                    <br />
                    <li>En los casos de soporte imputables al cliente se cobrar&'||'aacute; el costo de los materiales utilizados y la visita t&'||'eacute;cnica programada.</li>
                    <br />
                    <li>Servicio solo disponible para planes Home. </li>
                </ul>
            </div>    
            
            <br /><br />
            <div><b>NETFIBER:</b> </div>
            <br />
            <div class="clausulas">
                <ul>
                    <li>Fibra Invisible tiene un precio de $125,00+iva e incluye ​50mts de fibra invisible, conversor &'||'oacute;ptico el&'||'eacute;ctrico y switch de 4 puertos Gbps e instalaci&'||'oacute;n. El metro adicional de fibra tiene un costo de $3,00+iva.</li>
                    <br />
                    <li>Este servicio es de &'||'uacute;nico pago y esta disponible solo para las ciudades de Quito y Guayaquil.</li>
                    <br />
                    <li>Esta sujeto a restricciones de factibilidad geogr&'||'aacute;fica y t&'||'eacute;cnica. La velocidad ofertada depende de la capacidad y procesamiento que soporte el dispositivo final del cliente as&'||'iacute; como del router wifi y la capacidad del sitio remoto de contenido.</li>
                    <br />
                    <li>En caso de realizar la conexi&'||'oacute;n mediante wifi a 2.4Ghz la velocidad m&'||'aacute;xima que permite este tipo de tecnolog&'||'iacute;a que es de 40Mbps y en la banda de 5GHz llega hasta 100Mbps a una distancia de 3 metros y sin obst&'||'aacute;culos, en otras condiciones se tendr&'||'aacute;n velocidades menores.</li>
                </ul>
            </div>                

            <br /><br />
            <div><b>NETLIFECAM:</b> </div>
            <br />
            <div class="clausulas">
                <ul>
                    <li>Por medio del presente documento el CLIENTE deja constancia expresa de la aceptaci&'||'oacute;n de los T&'||'eacute;rminos y Condiciones para el Uso de NETLIFECAM, de la cual la compañ&'||'iacute;a NETLIFE, compañ&'||'iacute;a domiciliada en Quito, Ecuador, es su &'||'uacute;nica propietaria, creadora y desarrolladora. El acceso y uso de estas credenciales y este producto está sujeto a la aceptaci&'||'oacute;n de los siguientes T&'||'eacute;rminos y Condiciones que se encuentra en español. </li>
                    <br />
                    <li>Cambios en los T&'||'eacute;rminos y Condiciones.- NETLIFE se reserva el derecho a modificar estos T&'||'eacute;rminos y Condiciones a su entera discreci&'||'oacute;n y en cualquier momento, de acuerdo a necesidades corporativas; en tal sentido el CLIENTE acepta, autoriza y brinda su consentimiento por la presente a NETLIFE a administrar, sistematizar, procesar y archivar sus datos durante el tiempo de vigencia del contrato suscrito con NETLIFE y del producto denominado NETLIFE CAM y dem&'||'aacute;s procesos. Los Clientes podr&'||'aacute;n conocer cualquier modificaci&'||'oacute;n en estos T&'||'eacute;rminos y Condiciones a trav&'||'eacute;s de la publicaci&'||'oacute;n que NETLIFE realizar&'||'aacute; en su p&'||'aacute;gina web. Usar el producto luego de efectuado cualquier cambio constituye aceptaci&'||'oacute;n expresa por parte del Cliente de los nuevos Términos y Condiciones. NETLIFE podr&'||'aacute; emplear medios electr&'||'oacute;nicos, en todas las transacciones comerciales generadas, procesadas y aceptadas a trav&'||'eacute;s de llamadas telef&'||'oacute;nicas y sus modalidades similares digitales que gozan de igual valor que aquellas generadas personalmente como acuerdo de voluntades autorizada por el titular.  
                    <br />
                    Aplicaci&'||'oacute;n y Aceptaci&'||'oacute;n de los servicios.- La Aplicaci&'||'oacute;n para el contexto global requiere determinar definiciones  :    
                    <div class="clausulas">
                        <ul>
                            <li>Datos personales.- Cualquier informaci&'||'oacute;n concerniente a una persona natural identificada o identificable.</li>
                            <br />                            
                            <li>Titular.- La persona natural (TITULAR) a  quien  id&'||'eacute;ntica   o  corresponden  los  datos      personales. </li>
                            <br />                            
                            <li>Responsable.- Persona  natural o jur&'||'iacute;dica que decide  sobre  el  tratamiento  de los datos personales.</li>
                            <br />                            
                            <li>Tratamiento.- La obtenci&'||'oacute;n, uso (que incluye el acceso, manejo, aprovechamiento, transferencia o disposici&'||'oacute;n de datos personales), divulgaci&'||'oacute;n o almacenamiento de datos personales por cualquier medio.</li>
                            <br />                            
                            <li>Transferencia.-Toda comunicaci&'||'oacute;n de datos realizada a persona distinta del responsable o encargado del tratamiento.</li>
                            <br />  
                            <li>Consentimiento T&'||'aacute;cito.- Se entender&'||'aacute; que el titular ha consentido en el tratamiento de los datos, cuando habi&'||'eacute;ndose puesto a su disposici&'||'oacute;n el Aviso de Privacidad, no manifieste su oposici&'||'oacute;n.</li>
                            <br />  
                            <li>Finalidades Primarias.- Los datos personales que recabamos de usted, los utilizaremos para las siguientes finalidades que son necesarias para el servicio que solicita: fines personales, fines laborales, fines  bancarios.</li>
                            <br />  
                            <li>Formas de recabar sus datos personales.- Para las actividades señaladas en el presente Aviso de Privacidad, podemos recabar sus datos personales de distintas formas: cuando usted nos los proporciona directamente, cuando visita nuestro sitio web o utiliza nuestros servicios en línea, y cuando obtenemos informaci&'||'oacute;n a trav&'||'eacute;s de otras fuentes que est&'||'aacute;n permitidas por la ley.</li>
                            <br />  
                            <li>Datos personales que se recaban en forma directa: Recabamos sus datos personales de forma directa cuando usted mismo nos los proporciona por diversos medios.</li>
                            <br />  
                            <li>Datos personales que recabamos cuando visita nuestro sitio de internet o utiliza nuestros servicios en l&'||'iacute;nea: No recabamos sus datos personales de esta forma.</li>
                            <br />  
                            <li>Datos personales que recabamos a trav&'||'eacute;s de otras fuentes permitidas por la ley: No recabamos sus datos personales de esta forma.</li>
                            <br />  
                            <li>Im&'||'aacute;genes y sonidos recabados por c&'||'aacute;maras de Video NETLIFECAM: Las im&'||'aacute;genes y sonidos que se recaben por medio de c&'||'aacute;maras de Video NETLIFE CAM ser&'||'aacute;n utilizados para los fines de SEGURIDAD, USO PARTICULAR Y OTROS.  </li>
                            <br />  
                            <li>Uso de datos sensibles. - Se consideran datos sensibles aquellos afecten a la esfera m&'||'aacute;s &'||'iacute;ntima de su titular, o cuya utilizaci&'||'oacute;n indebida pueda dar origen a discriminaci&'||'oacute;n o conlleve un riesgo grave para &'||'eacute;ste. En el presente Aviso de Privacidad se omite el uso de datos personales considerados como    sensibles.</li>
                            <br />  
                            <li>Derechos del Cliente.- Usted tiene derecho de acceder a sus datos personales que poseemos y a los detalles del tratamiento de los mismos, as&'||'iacute; como a rectificarlos en caso de ser inexactos o incompletos; cancelarlos cuando considere que no se requieren para alguna de las  finalidades señalados en el presente Aviso de Privacidad, estén siendo utilizados para finalidades no consentidas o haya finalizado la relaci&'||'oacute;n contractual o de servicio, o bien, oponerse al tratamiento de los mismos para fines  específicos.</li>
                            <br />
                        </ul>        
                    </div>
                    </li>
                    <br />
                    <li>Los mecanismos que se han implementado para el ejercicio de dichos derechos, los cuales se conocen como derechos Arco mismos que se refieren a la rectificaci&'||'oacute;n, cancelaci&'||'oacute;n y oposici&'||'oacute;n del Titular respecto al tratamiento de sus datos personales.</li>
                    <br />
                    <li>Las partes expresan que el presente aviso, se regir&'||'aacute; por las disposiciones legales aplicables en el territorio ecuatoriano y de la legislaci&'||'oacute;n ecuatoriana y supra nacional vigente.</li>
                    <br />
                    <li>El cliente entiende que la instalaci&'||'oacute;n del servicio se realizar&'||'aacute; m&'||'aacute;ximo 10 metros del router WIfi para garantizar su calidad óptima. Esta distancia podr&'||'iacute;a ser menor y depender&'||'aacute; de la cantidad de obst&'||'aacute;culos e interferencias de señal que se detecten durante la instalaci&'||'oacute;n</li>
                    <br />
                    <li>La instalaci&'||'oacute;n del servicio incluye la visualizaci&'||'oacute;n de la c&'||'aacute;mara. No incluye cableado interno, configuraci&'||'oacute;n de red local, ni trabajos de conexi&'||'oacute;n el&'||'eacute;ctrica.	</li>
                    <br />
                    <li>La c&'||'aacute;mara que se instala para este servicio, en el caso de daño por negligencia del Cliente, &'||'eacute;ste asumir&'||'aacute; el valor total de su reposici&'||'oacute;n, valor que ser&'||'aacute; gravado en la facturaci&'||'oacute;n. </li>
                    <br />
                    <li>El cliente es absolutamente responsable de la informaci&'||'oacute;n o contenido del servicio contratado, as&'||'iacute; como de la transmisi&'||'oacute;n de &'||'eacute;sta a los Clientes de Internet.</li>
                    <br />
                    <li>El cliente est&'||'aacute; consciente que es el &'||'uacute;nico responsable de grabar y respaldar la informaci&'||'oacute;n de su propiedad que pueda derivarse de la visualizaci&'||'oacute;n remota.</li>
                    <br />
                    <li>El cliente libera y mantiene a salvo a MEGADATOS de los daños y perjuicios que se ocasionen por accesos no autorizados, robo, daño, destrucción o desviaci&'||'oacute;n de la informaci&'||'oacute;n, archivos o programas que se relacionen de manera directa o indirecta con el “Servicio” prestado por MEGADATOS.</li>
                    <br />
                    <li>El cliente libera y mantiene a salvo a MEGADATOS de cualquier reclamaci&'||'oacute;n, demanda y/o acci&'||'oacute;n legal que pudiera derivarse del uso que el cliente o terceras personas relacionadas hagan del servicio, que implique daño, alteraci&'||'oacute;n y/o modificaci&'||'oacute;n a la red, medios y/o infraestructura a trav&'||'eacute;s de la cual se presta el servicio.</li>
                    <br />
                    <li>POLITICA DE PRIVACIDAD.- Por la prestaci&'||'oacute;n de este producto, el “PRESTADOR” podrá recopilar informaci&'||'oacute;n de registro, informaci&'||'oacute;n que pasar&'||'aacute; a terceros cuando &'||'eacute;sta sea requerida por la ley o por acciones legales para las cuales ésta informaci&'||'oacute;n es relevante, como cuando se trate de una orden judicial o a prop&'||'oacute;sito para prevenir un delito o fraude. En cuyo caso se entender&'||'aacute; que el “CLIENTE” ha dado su permiso para revelar la informaci&'||'oacute;n constante por la ejecuci&'||'oacute;n del producto. PROPIEDAD.- El “CLIENTE” acepta que el “PRESTADOR” es el dueño y propietario de los derechos personales y reales sobre la Base de Datos que se proporcionar&'||'aacute; en este producto.  </li>
                    <br />
                    <li>Limitaci&'||'oacute;n de responsabilidad.- La Aplicaci&'||'oacute;n es descargada y utilizada por el CLIENTE de forma libre y voluntaria, por lo que renuncia a reclamar a NETLIFE cualquier tipo de indemnizaci&'||'oacute;n por el mal uso o funcionamiento de &'||'eacute;sta. Se deja expresa constancia que para el correcto funcionamiento del producto NETLIFE CAM se deben reunir ciertos requisitos t&'||'eacute;cnicos. Bajo ninguna circunstancia NETLIFE ser&'||'aacute; responsable de las p&'||'eacute;rdidas, daños y/o perjuicios que puedan presuntamente derivarse de la utilizaci&'||'oacute;n del dispositivo y del contenido, puesto que la responsabilidad limita a la provisi&'||'oacute;n del dispositivo(s), sin embargo el CLIENTE reconoce que el uso que d&'||'eacute; al dispositivo se enmarcar&'||'aacute; a las normas de la sana cr&'||'iacute;tica, las buenas costumbres y las leyes del Ecuador, por ello su mal uso ser&'||'aacute; derivado y entendido exclusivamente como responsabilidad del CLIENTE. Los Clientes deben utilizar este producto por su cuenta y riesgo. En ningún caso NETLIFE ser&'||'aacute; responsable por daños y/o perjuicios aun cuando éstos pudieran haber sido advertidos, as&'||'iacute; como no ser&'||'aacute; responsable de ning&'||'uacute;n daño o p&'||'eacute;rdida que pueda derivarse o relacionarse con el uso o falla del dispositivo, incluso en los casos derivados de uso inapropiado, impropio o fraudulento. La utilizaci&'||'oacute;n de NETLIFE CAM por el Cliente implica la aceptación por este &'||'uacute;ltimo de la obligaci&'||'oacute;n de indemnizar a NETLIFE o su personal por cualquier acci&'||'oacute;n, reclamo daño, p&'||'eacute;rdida y/o gasto, incluidas costas, honorarios de abogados, que se deriven de dicha utilizaci&'||'oacute;n. Adicionalmente el Cliente entiende y acepta que este producto NETLIFE CAM es una herramienta t&'||'eacute;cnica con cierto margen de tolerancia y no ofrece un resultado libre de error al 100% por lo tanto no constituye una prueba v&'||'aacute;lida para reclamos posteriores. Reconoce adem&'||'aacute;s que el dispositivo video grabar&'||'aacute; el entorno en donde sea instalada, por cuenta del Cliente. </li>
                    <br />
                    <li>El servicio tiene un tiempo de permanencia m&'||'iacute;nima de 24 meses. En caso de cancelaci&'||'oacute;n anticipada aplica la cl&'||'aacute;usula de pago de los descuentos a los que haya accedido por promociones, tales como Instalaci&'||'oacute;n, tarifas preferenciales, etc.</li>
                    <br />
                </ul>
            </div>    


            <br /><br />
            <div><b>PARAMOUNT+:</b> </div>
            <br />
            <div class="clausulas">
                <ul>
                    <li>Aceptaci&'||'oacute;n del producto.- Por medio del presente documento el USUARIO deja constancia expresa de la aceptaci&'||'oacute;n de los T&'||'eacute;rminos y Condiciones para el Uso del producto Paramount+. </li>
                    <br />
                    <li>Cambios en los T&'||'eacute;rminos y Condiciones.- NETLIFE se reserva el derecho a modificar estos T&'||'eacute;rminos y Condiciones a su entera discreci&'||'oacute;n y en cualquier momento, de acuerdo a necesidades corporativas; en tal sentido el USUARIO acepta, autoriza y brinda su consentimiento por la presente a NETLIFE para administrar, sistematizar, procesar y archivar sus datos durante el tiempo de vigencia del contrato suscrito con NETLIFE y del producto denominado Paramount+ y dem&'||'aacute;s procesos. Los Usuarios podr&'||'aacute;n conocer cualquier modificaci&'||'oacute;n en estos T&'||'eacute;rminos y Condiciones a trav&'||'eacute;s de la publicaci&'||'oacute;n que NETLIFE realizar&'||'aacute; en su p&'||'aacute;gina web, as&'||'iacute; mismo el CLIENTE podr&'||'aacute; usar el producto luego de efectuado cualquier cambio que constituye aceptaci&'||'oacute;n expresa por parte del Usuario de los nuevos T&'||'eacute;rminos y Condiciones. NETLIFE podr&'||'aacute; emplear medios electr&'||'oacute;nicos, en todas las transacciones comerciales generadas, procesadas y aceptadas a trav&'||'eacute;s de llamadas telef&'||'oacute;nicas y sus modalidades similares digitales que gozan de igual valor que aquellas generadas personalmente como acuerdo de voluntades autorizadas por el titular. </li>
                    <br />
                    <li>Aplicaci&'||'oacute;n y Aceptaci&'||'oacute;n de los productos.-  El m&'||'eacute;todo de entrega de este producto (Usuario y Contraseña de acceso a la plataforma), es mediante env&'||'iacute;o de correo electr&'||'oacute;nico y/o sms al correo registrado por el cliente en su contrato.  Este correo debe ser un correo electr&'||'oacute;nico v&'||'aacute;lido. Es de absoluta responsabilidad del cliente verificar que el correo no se encuentre alojado en la carpeta de correo no deseado(spam). En caso de requerirlo, el cliente podr&'||'aacute; solicitar el reenv&'||'iacute;o de este. El cliente tendr&'||'aacute; acceso a la plataforma mediante enlace Web o a trav&'||'eacute;s del aplicativo m&'||'oacute;vil de “Paramount+”. Viacom International Inc. y Megadatos S.A. se reservan el derecho, con o sin aviso previo de: i) cambiar las descripciones, im&'||'aacute;genes y referencias relativas a productos y caracter&'||'iacute;sticas; ii) limitar la cantidad disponible de cualquier producto; iii) aceptar o imponer condiciones sobre la aceptaci&'||'oacute;n de cualquier cup&'||'oacute;n, c&'||'oacute;digo de cup&'||'oacute;n, c&'||'oacute;digo promocional o cualquier otra promoci&'||'oacute;n similar; iv) prohibir a cualquier usuario a realizar cualquiera o todas las transacciones; y/o v) negarnos a brindarle a cualquier usuario alg&'||'uacute;n producto. El precio y la disponibilidad de cualquier producto est&'||'aacute;n sujetos a cambio sin aviso. En caso de requerirlo, el cliente podr&'||'aacute; recibir soporte respecto cuestiones t&'||'eacute;cnicas relacionadas con la plataforma como reproducci&'||'oacute;n de video, contenidos no disponibles, errores en las im&'||'aacute;genes de los contenidos, entre otros; a trav&'||'eacute;s, del chat y help center email de la plataforma de Paramount+; así como, la secci&'||'oacute;n de Preguntas Frecuentes en la misma plataforma. Adicional, podr&'||'aacute; contactarse al 392000 para recibir soporte. En caso de que el cliente sea beneficiario de una promoci&'||'oacute;n/descuento, el cliente entiende que el incumplimiento de los lineamientos establecidos para recibir dicha promoci&'||'oacute;n/descuento resulten en el cobro de los beneficios entregados al mismo. </li>
                    <br />
                    <li>Aplicaci&'||'oacute;n y Aceptaci&'||'oacute;n de los servicios.- La Aplicaci&'||'oacute;n para el contexto global requiere determinar definiciones : Titular.- La persona natural (TITULAR) a quien identifica o corresponden los datos personales. Responsable.- Persona natural o jur&'||'iacute;dica que decide sobre el tratamiento de los datos personales. Tratamiento.- La obtenci&'||'oacute;n, uso (que incluye el acceso, manejo, aprovechamiento, transferencia o disposici&'||'oacute;n de datos personales), divulgaci&'||'oacute;n o almacenamiento de datos personales por cualquier medio. Transferencia.-Toda comunicaci&'||'oacute;n de datos realizada a persona distinta del responsable o encargado del tratamiento. Consentimiento T&'||'aacute;cito.- Se entender&'||'aacute; que el titular ha consentido en el tratamiento de los datos, cuando habi&'||'eacute;ndose puesto a su disposici&'||'oacute;n el Aviso de Privacidad, no manifieste su oposici&'||'oacute;n. Finalidades Primarias.- Los datos personales que recabamos de usted, los utilizaremos para las siguientes finalidades que son necesarias para el producto que solicita: fines personales, fines laborales, fines   bancarios. Formas de recabar sus datos personales.- Para las actividades señaladas en el presente Aviso de Privacidad, podemos recabar sus datos personales de distintas formas: cuando usted nos los proporciona directamente, cuando visita nuestro sitio web o utiliza nuestros productos en línea, y cuando obtenemos informaci&'||'oacute;n a trav&'||'eacute;s de otras fuentes que est&'||'aacute;n permitidas por la ley. Datos personales que se recaban en forma directa: Recabamos sus datos personales de forma directa cuando usted mismo nos los proporciona por diversos medios. Uso de datos sensibles.- Se consideran datos sensibles aquellos que afecten a la esfera más &'||'iacute;ntima de su titular, o cuya utilizaci&'||'oacute;n indebida pueda dar origen a discriminaci&'||'oacute;n o conlleve un riesgo grave para &'||'eacute;ste. En el presente Aviso de Privacidad se omite el uso de datos personales considerados como sensibles. Limitaci&'||'oacute;n  o divulgaci&'||'oacute;n de sus datos personales.- El responsable de la informaci&'||'oacute;n se compromete a realizar &'||'uacute;nicamente las siguientes acciones, respecto a su informaci&'||'oacute;n notificaciones por diferentes v&'||'iacute;as.  </li>
                    <br />
                    <li>El producto de Paramount+ ser&'||'aacute; prestado por NETLIFE en adelante el “PRESTADOR” que ser&'||'aacute; brindado a usted usuario en adelante “CLIENTE” bajo los t&'||'eacute;rminos y condiciones previstos en el presente contrato. Al ingresar y usar este sitio Web el “CLIENTE” expresa su voluntad y acepta los t&'||'eacute;rminos y condiciones establecidos. POL&'||'Iacute;TICA DE PRIVACIDAD.- Por la prestaci&'||'oacute;n de este producto, el “PRESTADOR” podr&'||'aacute; recopilar informaci&'||'oacute;n de registro, informaci&'||'oacute;n que pasar&'||'aacute; a terceros cuando &'||'eacute;sta sea requerida por la ley o por acciones legales para las cuales ésta informaci&'||'oacute;n es relevante, como cuando se trate de una orden judicial o a prop&'||'oacute;sito para prevenir un delito o fraude. En cuyo caso se entender&'||'aacute; que el “CLIENTE” ha dado su permiso para revelar la informaci&'||'oacute;n constante por la ejecución del producto. PROPIEDAD.- El “CLIENTE” acepta que el “PRESTADOR” es el dueño y propietario de los derechos personales y reales sobre la Base de Datos que se proporcionar&'||'aacute; en este producto.  Los t&'||'eacute;rminos de uso de la plataforma, ser&'||'aacute;n aquellos definidos por Viacom International Inc , detallados en el siguiente link https://www.paramountmas.com/ec/terminos-de-uso. La pol&'||'iacute;tica de privacidad de la plataforma, ser&'||'aacute; aquella definida por Viacom International Inc , en el siguiente link https://www.paramountmas.com/ec/politica-de-privacidad </li>
                </ul>
            </div>

            <br /><br />
            <div><b>NOGGIN+:</b> </div>
            <br />
            <div class="clausulas">
                <ul>
                    <li>Aceptaci&'||'oacute;n del producto.- Por medio del presente documento el USUARIO deja constancia expresa de la aceptaci&'||'oacute;n de los T&'||'eacute;rminos y Condiciones para el Uso del producto Noggin. </li>
                    <br />
                    <li>Cambios en los T&'||'eacute;rminos y Condiciones.- NETLIFE se reserva el derecho a modificar estos T&'||'eacute;rminos y Condiciones a su entera discreci&'||'oacute;n y en cualquier momento, de acuerdo a necesidades corporativas; en tal sentido el USUARIO acepta, autoriza y brinda su consentimiento por la presente a NETLIFE para administrar, sistematizar, procesar y archivar sus datos durante el tiempo de vigencia del contrato suscrito con NETLIFE y del producto denominado Noggin y dem&'||'aacute;s procesos. Los Usuarios podr&'||'aacute;n conocer cualquier modificaci&'||'oacute;n en estos T&'||'eacute;rminos y Condiciones a trav&'||'eacute;s de la publicaci&'||'oacute;n que NETLIFE realizar&'||'aacute; en su p&'||'aacute;gina web, as&'||'iacute; mismo el CLIENTE podr&'||'aacute; usar el producto luego de efectuado cualquier cambio que constituye aceptaci&'||'oacute;n expresa por parte del Usuario de los nuevos T&'||'eacute;rminos y Condiciones. NETLIFE podrá emplear medios electr&'||'oacute;nicos, en todas las transacciones comerciales generadas, procesadas y aceptadas a trav&'||'eacute;s de llamadas telef&'||'oacute;nicas y sus modalidades similares digitales que gozan de igual valor que aquellas generadas personalmente como acuerdo de voluntades autorizadas por el titular. </li>
                    <br />
                    <li>Aplicaci&'||'oacute;n y Aceptaci&'||'oacute;n de los productos.-  El m&'||'eacute;todo de entrega de este producto (Usuario y Contraseña de acceso a la plataforma), es mediante env&'||'iacute;o de correo electr&'||'oacute;nico y/o sms al correo registrado por el cliente en su contrato.  Este correo debe ser un correo electr&'||'oacute;nico v&'||'aacute;lido. Es de absoluta responsabilidad del cliente verificar que el correo no se encuentre alojado en la carpeta de correo no deseado(spam). En caso de requerirlo, el cliente podr&'||'aacute; solicitar el reenv&'||'iacute;o de este. El cliente tendr&'||'aacute; acceso a la plataforma mediante enlace Web o a trav&'||'eacute;s del aplicativo m&'||'oacute;vil de “Noggin”. Viacom International Inc. y Megadatos S.A. se reservan el derecho, con o sin aviso previo de: i) cambiar las descripciones, im&'||'aacute;genes y referencias relativas a productos y caracter&'||'iacute;sticas; ii) limitar la cantidad disponible de cualquier producto; iii) aceptar o imponer condiciones sobre la aceptaci&'||'oacute;n de cualquier cup&'||'oacute;n, c&'||'oacute;digo de cup&'||'oacute;n, c&'||'oacute;digo promocional o cualquier otra promoci&'||'oacute;n similar; iv) prohibir a cualquier usuario a realizar cualquiera o todas las transacciones; y/o v) negarnos a brindarle a cualquier usuario algún producto. El precio y la disponibilidad de cualquier producto est&'||'aacute;n sujetos a cambio sin aviso. En caso de requerirlo, el cliente podr&'||'aacute; recibir soporte respecto cuestiones t&'||'eacute;cnicas relacionadas con la plataforma como reproducci&'||'oacute;n de video, contenidos no disponibles, errores en las im&'||'aacute;genes de los contenidos, entre otros; a trav&'||'eacute;s, del chat y help center email de la plataforma de Paramount+; as&'||'iacute; como, la secci&'||'oacute;n de Preguntas Frecuentes en la misma plataforma. Adicional, podr&'||'aacute; contactarse al 392000 para recibir soporte. En caso de que el cliente sea beneficiario de una promoci&'||'oacute;n/descuento, el cliente entiende que el incumplimiento de los lineamientos establecidos para recibir dicha promoci&'||'oacute;n/descuento resulten en el cobro de los beneficios entregados al mismo. </li>
                    <br />
                    <li>Aplicaci&'||'oacute;n y Aceptaci&'||'oacute;n de los servicios.- La Aplicaci&'||'oacute;n para el contexto global requiere determinar definiciones : Titular.- La persona natural (TITULAR) a quien identifica o corresponden los datos personales. Responsable.- Persona natural o jur&'||'iacute;dica que decide sobre el tratamiento de los datos personales. Tratamiento.- La obtenci&'||'oacute;n, uso (que incluye el acceso, manejo, aprovechamiento, transferencia o disposici&'||'oacute;n de datos personales), divulgaci&'||'oacute;n o almacenamiento de datos personales por cualquier medio. Transferencia.-Toda comunicaci&'||'oacute;n de datos realizada a persona distinta del responsable o encargado del tratamiento. Consentimiento T&'||'aacute;cito.- Se entender&'||'aacute; que el titular ha consentido en el tratamiento de los datos, cuando habi&'||'eacute;ndose puesto a su disposici&'||'oacute;n el Aviso de Privacidad, no manifieste su oposici&'||'oacute;n. Finalidades Primarias.- Los datos personales que recabamos de usted, los utilizaremos para las siguientes finalidades que son necesarias para el producto que solicita: fines personales, fines laborales, fines   bancarios. Formas de recabar sus datos personales.- Para las actividades señaladas en el presente Aviso de Privacidad, podemos recabar sus datos personales de distintas formas: cuando usted nos los proporciona directamente, cuando visita nuestro sitio web o utiliza nuestros productos en línea, y cuando obtenemos informaci&'||'oacute;n a trav&'||'eacute;s de otras fuentes que est&'||'aacute;n permitidas por la ley. Datos personales que se recaban en forma directa: Recabamos sus datos personales de forma directa cuando usted mismo nos los proporciona por diversos medios. Uso de datos sensibles.- Se consideran datos sensibles aquellos que afecten a la esfera más &'||'iacute;ntima de su titular, o cuya utilizaci&'||'oacute;n indebida pueda dar origen a discriminaci&'||'oacute;n o conlleve un riesgo grave para &'||'eacute;ste. En el presente Aviso de Privacidad se omite el uso de datos personales considerados como sensibles. Limitaci&'||'oacute;n  o divulgaci&'||'oacute;n de sus datos personales.- El responsable de la informaci&'||'oacute;n se compromete a realizar &'||'uacute;nicamente las siguientes acciones, respecto a su informaci&'||'oacute;n notificaciones por diferentes v&'||'iacute;as.  </li>
                    <br />
                    <li>El producto de Noggin ser&'||'aacute; prestado por NETLIFE en adelante el “PRESTADOR” que ser&'||'aacute; brindado a usted usuario en adelante “CLIENTE” bajo los t&'||'eacute;rminos y condiciones previstos en el presente contrato. Al ingresar y usar este sitio Web el “CLIENTE” expresa su voluntad y acepta los t&'||'eacute;rminos y condiciones establecidos. POL&'||'Iacute;TICA DE PRIVACIDAD.- Por la prestaci&'||'oacute;n de este producto, el “PRESTADOR” podr&'||'aacute; recopilar informaci&'||'oacute;n de registro, informaci&'||'oacute;n que pasar&'||'aacute; a terceros cuando &'||'eacute;sta sea requerida por la ley o por acciones legales para las cuales ésta informaci&'||'oacute;n es relevante, como cuando se trate de una orden judicial o a prop&'||'oacute;sito para prevenir un delito o fraude. En cuyo caso se entender&'||'aacute; que el “CLIENTE” ha dado su permiso para revelar la informaci&'||'oacute;n constante por la ejecución del producto. PROPIEDAD.- El “CLIENTE” acepta que el “PRESTADOR” es el dueño y propietario de los derechos personales y reales sobre la Base de Datos que se proporcionar&'||'aacute; en este producto.  Los t&'||'eacute;rminos de uso de la plataforma, ser&'||'aacute;n aquellos definidos por Viacom International Inc , detallados en el siguiente link https://www.nickelodeon.la/legal/373cyf/terminos-y-condiciones. La pol&'||'iacute;tica de privacidad de la plataforma, ser&'||'aacute; aquella definida por Viacom International Inc , en el siguiente link https://www.nickelodeon.la/legal/zs8ejr/politica-de-privacidad </li>
                </ul>
            </div>



            <br /><br />
            <div>
              El cliente con la sola suscripci&'||'oacute;n voluntaria del presente Adendum confirma que ha le&'||'iacute;do y conoce las condiciones de uso de los equipos y servicios descritos a ser contratados. La informaci&'||'oacute;n proporcionada en el presente documento, el cliente autoriza a Megadatos para uso y tratamiento  acorde a la normativa legal vigente a fin al contrato de adhesi&'||'oacute;n.
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
                <div id="colCell" class="col-width-50" style="text-align:right">ver-07 | Ene-2021</div>
            </div>
        </div>
</body>

</html>');

UPDATE DB_FIRMAELECT.ADM_EMPRESA_PLANTILLA
SET HTML= bada
where COD_PLANTILLA = 'adendumMegaDatos';
commit;
end;

/