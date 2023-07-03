/**
 *
 * Rollback de actualización de plantilla adendum TM Comercial
 *
 * @author Christian Jaramillo Espinoza <cjaramilloe@telconet.ec>
 * @version 1.0 24-11-2020
 *
 **/

UPDATE DB_FIRMAELECT.ADM_EMPRESA_PLANTILLA
SET HTML=EMPTY_CLOB()
where COD_PLANTILLA = 'adendumMegaDatos';
commit;

SET SERVEROUTPUT ON 200000;
declare
    bada clob:='<!DOCTYPE html>';
begin

DBMS_LOB.APPEND(bada, '
    <!DOCTYPE html>
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
            <td class="col-width-75" style="font-size:14px;">FE DE ERRATAS: este adendum es emitido como correcci&oacute;n al adenum del contrato $!numeroContrato</td>
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
                <div id="col" class="col-width-15">Tel&&eacute;fono contacto:</div>
                <div id="col" class="col-width-15 labelGris">$!celularContacto<span class="textPadding"></span></div>
                <div id="col" class="col-width-2-5"></div>
                <div id="col" class="col-width-15">Tel&&eacute;fono fijo contacto:</div>
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
                        <li>Netlife Defense soporta: Equipos de escritorio y portátiles: Windows 10/8.1 /8 /7 o superior; OS X 10.12 – macOS 10.13 o superiores; Tablets: Windows 10 / 8& 8.1 / Pro (64 bits); iOS 9.0 o posterior; Smartphones: Android 4.1 o posterior, iOS 9.0 o posterior (solo para navegación; a través, de Kaspersky Safe Browser).</li>
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

    </html>

');

UPDATE DB_FIRMAELECT.ADM_EMPRESA_PLANTILLA
SET HTML= bada
where COD_PLANTILLA = 'adendumMegaDatos';

COMMIT;
END;

        