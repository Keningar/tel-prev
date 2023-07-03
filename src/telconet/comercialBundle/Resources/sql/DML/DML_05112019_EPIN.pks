UPDATE DB_FIRMAELECT.ADM_EMPRESA_PLANTILLA
SET HTML=EMPTY_CLOB()
where COD_PLANTILLA = 'adendumMegaDatos';
commit;

SET SERVEROUTPUT ON 200000;
declare
    bada clob:='<!DOCTYPE html>';
begin

--DBMS_LOB.FREETEMPORARY(bada);

--dbms_lob.createtemporary(bada, TRUE);
DBMS_LOB.APPEND(bada, '<html>

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

        /* // ==========================================
               // Vi&'||'ntilde;etas para las clausulas
               // ==========================================*/
        .clausulas ul {
            list-style: none;
            /* Remove list bullets */
            padding: 0;
            margin: 0;
        }

        .clausulas li {
            padding-left: 16px;
        }

        .clausulas li:before {
            content: "-";
            padding-right: 5px;
        }

        /* // ==========================================
               // Clases de manejo de tama&'||'ntilde;o de columnas
               // ==========================================*/

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

    <div align="center" style="float: right;">
           <img src="http://images.telconet.net/others/telcos/logo_netlife.png" alt="log" title="NETLIFE"
          height="40" />

        <table id="netlife" style="padding-right: 30px; ">
            <tr>
               <td><div>FO-ATC-02</div></td>
               <td><div>FECHA:</div></td>
               <td><div>$!fechaActual</div></td>
               <td><div>VERSION 03</div></td>        

            </tr>
        </table>
    </div>



    <div style="clear: both;"></div>
    <div class="labelBlock">REQUERIMIENTOS DE SERVICIOS</div>

    <br /><br />

    <div style="clear: both;"></div>
    <div class="labelBlock">DATOS GENERALES</div>

    <div id="contenedor" class="col-width-100">
        <div id="row">
            <div id="col" class="col-width-15"><b>Clientes</b></div>
            <div id="col" class="col-width-45 labelGris">
                $!nombresApellidos
                <span class="textPadding"></span>
            </div>
        </div>
        <div id="row">
            <div id="col" class="col-width-15"><b>Login:</b></div>
            <div id="col" class="col-width-30 labelGris">
                $!loginPunto
                <span class="textPadding">
                </span>
            </div>
            <div id="col" class="col-width-5"></div>
            <div id="col" class="col-width-5" style="text-align : left; padding-right: 1px;"><b>C.I:</b></div>
            <div class="box">$!isCedula</div>

            <div id="col" class="col-width-5" style="text-align : right; padding-right: 1px;"><b>RUC</b></div>
            <div class="box">$!isRuc</div>
            <div id="col" class="col-width-10" style="text-align : right; padding-right: 1px;"><b>PASAPORTE</b></div>
            <div class="box">$!isPasaporte</div>
            <div id="col" class="col-width-20 labelGris">
                $!identificacion
                <span class="textPadding">
                </span>
            </div>

        </div>
        <div id="row">
            <div id="col" class="col-width-15"><b>Fecha de Solicitud:</b></div>
            <div id="col" class="col-width-45 labelGris">
                $!fechaActual
                <span class="textPadding">
                </span>
            </div>
            <div id="col" class="col-width-15"><b>N&'||'uacute;mero de Contrato:</b></div>
            <div id="col" class="col-width-20 labelGris">
                $!numeroContrato
                <span class="textPadding">
                </span>
            </div>

        </div>
        <div id="row">
            <div id="col" class="col-width-15"><b>Categor&' || 'iacutea Plan:</b></div>
            <div id="col" class="col-width-5" style="text-align : left; padding-right: 1px;"><b>HOME</b></div>
            <div class="box">$!isHome</div>

            <div id="col" class="col-width-5" style="text-align : right; padding-right: 1px;"><b>PRO</b></div>
            <div class="box">$!isPro</div>
            <div id="col" class="col-width-15" style="text-align : right; padding-right: 1px;"><b>PYME</b></div>
            <div class="box">$!isPyme</div>

        </div>
        <div id="row">
            <div id="col" class="col-width-15"><b>Direcci&'||'oacute;n del Punto:</b></div>
            <div id="col" class="col-width-45 labelGris">
                <span class="textPadding">$!direccionServicio</span>
            </div>
        </div>
    </div>


    <div style="clear: both;"></div><br /><br /><br />
    <div class="labelBlock">CAMBIO DE PLAN</div>
        <div id="contenedor" class="col-width-100">
  
          <div id="row">
              <div id="col" class="col-width-5"></div>
              <div id="col" class="col-width-20" style="text-align : left; padding-right: 1px;"><b>CAMBIO DE PLAN</b>
              </div>
              <div class="box">$!isHome</div>
  
              <div id="col" class="col-width-15" style="text-align : right; padding-right: 1px;"><b>UPGRADE</b></div>
              <div class="box">$!isPro</div>
              <div id="col" class="col-width-15" style="text-align : right; padding-right: 1px;"><b>DOWNGRADE</b></div>
              <div class="box">$!isPyme</div>
  
          </div>
          <div id="row">
              <div id="col" class="col-width-15"><b>PLAN ANTERIOR:</b></div>
              <div id="col" class="col-width-15 labelGris">
                  <span class="textPadding">$!nombrePlanAnterior</span>
              </div>
              <div id="col" class="col-width-10"><b>VALOR:</b></div>
              <div id="col" class="col-width-10 labelGris">
                  <span class="textPadding">$!valorPlanAnterior</span>
              </div>
              <div id="col" class="col-width-15"><b>NUEVO PLAN:</b></div>
              <div id="col" class="col-width-15 labelGris">
                  <span class="textPadding">$!nombrePlanActual</span>
              </div>
              <div id="col" class="col-width-10"><b>VALOR:</b></div>
              <div id="col" class="col-width-10 labelGris">
                  <span class="textPadding">$!valorPlanActual</span>
              </div>
              <p>Para cualquier cambio de plan el cliente podrá escoger entre los planes vigentes en la fecha de la
                  solicitud.</p>
          </div>
        </div>
        <div style="clear: both;"></div><br /><br /><br />
        <div class="labelBlock">SOLICITUD DE SERVICIOS ADICIONALES</div>

            <div style="clear: both;"></div><br /><br /><br />
        </div>
        <div style="width:100%;  vertical-align:top;">
            <div class="labelBlock textCenter" style="margin: 0; border:1px black solid;">SERVICIOS Y TARIFAS</div>
            <table class="box-section-content col-width-100 borderTable"
                style="border-collapse:collapse; border-spacing:0; ">
                <tr>
                    <td class="line-height textCenter labelGris" style="width: 39%"><b>SERVICIO</b></td>
                    <td class="line-height textCenter labelGris" style="width: 10%"><b>CANTIDAD</b></td>
                    <td class="line-height textCenter labelGris" style="width: 13%"><b>VALOR UNICO</b></td>
                    <td class="line-height textCenter labelGris" style="width: 11%"><b>VALOR MES</b></td>
                    <td class="line-height textCenter labelGris" style="width: 14%"><b>VALOR TOTAL</b></td>
                    <td class="line-height textCenter labelGris" style="width: 13%"><b>OBSERVACIONES</b></td>
                </tr>
                <tr>
                    <td class="line-height labelGris">Internet Protegido</td>
                    <td class="line-height textCenter">$!productoInternet1Cantidad</td>
                    <td class="line-height textCenter">$!productoInternet1Instalacion</td>
                    <td class="line-height textCenter">$!productoInternet1Precio</td>
                    <td class="line-height textCenter">$!productoInternet1Precio</td>
                    <td class="line-height textCenter">$!productoInternet1Observaciones</td>
                </tr>
                <tr>
                    <td class="line-height labelGris">Netlifecam</td>
                    <td class="line-height textCenter">$!productoNetLifeCamCantidad</td>
                    <td class="line-height textCenter">$!productoNetLifeCamInstalacion</td>
                    <td class="line-height textCenter">$!productoNetLifeCamInstalacionPrecio</td>
                    <td class="line-height textCenter">$!productoNetLifeCamPrecio</td>
                    <td class="line-height textCenter">$!productoNetLifeCamObservaciones</td>
                </tr>
                <tr>
                    <td class="line-height labelGris">Office 365 home</td>
                    <td class="line-height textCenter">$!productoIpAdicionalCantidad</td>
                    <td class="line-height textCenter">$!productoIpAdicionalInstalacion</td>
                    <td class="line-height textCenter">$!productoIpAdicionalPrecio</td>
                    <td class="line-height textCenter">$!productoIpAdicionalPrecio</td>
                    <td class="line-height textCenter">$!productoIpAdicionalObservaciones</td>
                </tr>
                <tr>
                    <td class="line-height labelGris">WIFI E6500</td>
                    <td class="line-height textCenter">$!productoWifiCantidad</td>
                    <td class="line-height textCenter">$!productoWifiInstalacion</td>
                    <td class="line-height textCenter">$!productoWifiPrecio</td>
                    <td class="line-height textCenter">$!productoWifiPrecio</td>
                    <td class="line-height textCenter">$!productoWifiObservaciones</td>
                </tr>
                <tr>
                    <td class="line-height labelGris">Netlife Zone</td>
                    <td class="line-height textCenter">$!productoNetLifeZoneCantidad</td>
                    <td class="line-height textCenter">$!productoNetLifeZoneInstalacion</td>
                    <td class="line-height textCenter">$!productoNetLifeZonePrecio</td>
                    <td class="line-height textCenter">$!productoNetLifeZonePrecio</td>
                    <td class="line-height textCenter">$!productoNetLifeZoneObservaciones</td>
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
                    <td class="line-height textCenter">$!subtotalInstalacion1</td>
                    <td class="line-height textCenter">SUBTOTAL:</td>
                    <td class="line-height textCenter">$!subtotal</td>
                    <td class="line-height textCenter" style="border-bottom:1px white solid;"></td>
                </tr>
');
                                    
DBMS_LOB.APPEND(bada, '                <tr>
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
                <tr>
                    <td colspan="3">

                        <div id="row">
                            <div id="colCell" class="col-width-10 textRight"><b>Promoci&'||'oacute;n:</b></div>
                            <div>$!descInstalacion</div>
                            <div id="colCell" class="col-width-30 textRight">Descuento instalaci&'||'oacute;n:</div>
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
        </div>


        <div style="clear: both;"><br /></div>
        <div class="col-width-100" style="text-align: justify;">
            <div> <b>INFORMACION Y CONDICIONES ADICIONALES: </b> </div>
            <br />
            <div class="clausulas">
                <ul>
                    <li>El cliente conoce y acepta que este documento representa un adendum de servicios adicionales
                        contratados que forma parte del contrato principal de NETLIFE y cuenta con la misma forma de
                        pago acordada en el mismo.</li>
                    <li>El cliente acepta y declara que conoce toda la informaci&'||'oacute;n referente al servicio
                        /producto adicional contratado y est&'||'aacute; de acuerdo con todos los items escritos en este
                        adendum. El cliente conoce y entiende que el servicio contratado con NETLIFE no incluye cableado
                        interno o configuraci&'||'oacute;n de la red local del cliente.</li>
                </ul>
            </div>

            <br /><br />
            <div><b>CORREO: </b> </div>
            <br />
            <div class="clausulas">
                <ul>
                    <li>Los planes de NETLIFE no incluyen cuentas de correo electr&'||'oacute;nico. En caso de que el
                        cliente lo solicite es posible agregar una cuenta de correo electr&'||'oacute;nico con dominio
                        netlife.ec por un valor adicional. Esta cuenta de correo no incluye el almacenamiento del mismo,
                        sino que es el cliente quien deber&'||'aacute; almacenar los correos que lleguen a su cuenta por lo
                        que MEGADATOS no se responsabiliza de ninguna forma por la p&'||'eacute;rdida de almacenamiento de
                        ning&'||'uacute;n contenido o informaci&'||'oacute;n.</li>
                </ul>
            </div>

            <br /><br />
            <div><b>NETLIFECAM: </b> </div>
            <br />
            <div class="clausulas">
                <ul>
                    <li>El cliente entiende que la instalaci&'||'oacute;n del servicio se realizar&'||'aacute; m&'||'aacute;ximo 10
                        metros del router wifi para garantizar su calidad &'||'oacute;ptima. Esta distancia podr&'||'iacute;a
                        ser menor y depender&'||'aacute; de la cantidad de obst&'||'aacute;culos e interferencias de señal.</li>
                    <li>La c&'||'aacute;mara que se instala para este servicio pasa a ser de propiedad del cliente, sin
                        embargo, en el caso de daño por negligencia del Cliente, &'||'eacute;ste asumir&'||'aacute; el valor
                        total de su reposici&'||'oacute;n. El costo de la c&'||'aacute;mara es de USD$69,99 (mas IVA).</li>
                    <li>El cliente es reponsable por el uso y seguridad de la clave de acceso a la visualizaci&'||'oacute;n
                        remota que provea MEGADATOS y deslinda desde ya a MEGADATOS de cualquier responsabilidad por
                        p&'||'eacute;rdida o mal uso que pueda derivarse del manejo no adecuado que el cliente pueda dar a
                        esta clave.</li>
                    <li>El cliente es responsable de realizar una actualizaci&'||'oacute;n frecuente de su clave para evitar
                        cualquier ataque que pueda sufrir su acceso.</li>
                    <li>El cliente es responsable de la informaci&'||'oacute;n o contenido del servicio contratado,
                        as&'||'iacute; como de la transmisi&'||'oacute;n de &'||'eacute;sta a los usuarios de Internet.</li>
                    <li>El cliente es responsable de grabar y respaldar la informaci&'||'oacute;n de su propiedad que pueda
                        derivarse de la visualizaci&'||'oacute;n remota.</li>
                    <li>El cliente libera y mantiene a salvo a MEGADATOS de los daños y perjuicios que se ocasionen por
                        accesos no autorizados, robo, daño, destrucci&'||'oacute;n o desviaci&'||'oacute;n de la
                        informaci&'||'oacute;n, archivos o programas que se relacionen de manera directa o indirecta con el
                        “Servicio” prestado por MEGADATOS.</li>
                    <li>El cliente libera y mantiene a salvo a MEGADATOS de cualquier reclamaci&'||'oacute;n, demanda y/o
                        acci&'||'oacute;n legal que pudiera derivarse del uso que el cliente o terceras personas
                        relacionadas hagan del servicio, que implique daño, alteraci&'||'oacute;n y/o modificaci&'||'oacute;n a
                        la red, medios y/o infraestructura a trav&'||'eacute;s de la cual se presta el servicio.</li>
                </ul>
            </div>

            <br /><br />
            <div><b>OFFICE 365 HOME: </b> </div>
            <br />
            <div class="clausulas">
                <ul>
                    <li>Para poder activar este producto es requisito contar con una cuenta microsoft (Cuentas
                        @hotmail.com, @outlook.com, etc )</li>
                    <li>El producto no incluye renovaci&'||'oacute;n autom&'||'aacute;tica de licencia por lo que al finalizar
                        su vigencia el cliente deber&'||'aacute; adquirir una nueva.</li>
                    <li>Este producto se puede instalar en PCs y tabletas Windows que ejecuten Windows 7 o una
                        versi&'||'oacute;n posterior, y equipos Mac con Mac OS X 10.6 o una versi&'||'oacute;n posterior. Office
                        para iPad se puede instalar en iPads que ejecuten la &'||'uacute;ltima versi&'||'oacute;n de iOS. Office
                        Mobile para iPhone se puede instalar en tel&'||'eacute;fonos que ejecuten iOS 6.0 o una
                        versi&'||'oacute;n posterior. Office Mobile para tel&'||'eacute;fonos Android se puede instalar en
                        tel&'||'eacute;fonos que ejecuten OS 4.0 o una versi&'||'oacute;n posterior. Para obtener más
                        informaci&'||'oacute;n sobre los dispositivos y requerimientos, visite www.office.com/information.
                    </li>
                    <li>La entrega de este producto no incluye el servicio de instalaci&'||'oacute;n del mismo en
                        ning&'||'uacute;n dispositivo. Para tal efecto, dentro del producto se encuentra una gu&'||'iacute;a de
                        instalaci&'||'oacute;n a seguir por el cliente. El cliente es responsable de la instalaci&'||'oacute;n y
                        configuraci&'||'oacute;n del producto en sus dispositivos y usuarios.</li>
                    <li>El canal de soporte a consultas, dudas o requerimientos espec&'||'iacute;ficos del producto Office
                        365 HOME podr&'||'aacute; ser realizado a trav&'||'eacute;s del tel&'||'eacute;fono: 1-800-010-288</li>
                    <li>Los pasos para instalar y empezar a utilizar office 365 HOME se encuentran en el siguiente link:
                        office.com/setup. Para administrar los dispositivos y cuentas de su licencia office 365 HOME
                        acceda al link: office.com/myaccount.</li>
                </ul>
            </div>

            <br /><br />
            <div><b>EQUIPO WiFi E6500: </b> </div>
            <br />
            <div class="clausulas">
                <ul>
                    <li>El equipo es propiedad del cliente y tiene una garantía de 1 año por daños de fábrica. En caso
                        de que se encuentre daño por otro motivo, no aplica garantía.</li>
                    <li>El cliente conoce y acepta que, para garantizar la calidad del servicio, este equipo será
                        administrado por NETLIFE mientras dure la prestación del servicio.</li>
                    <li>El equipo WiFi provisto por NETIFE tiene puertos alámbricos que permiten la utilización optima
                        de la velocidad ofertada en el plan contratado, además cuenta con conexión WiFi a una frecuencia
                        de 5Ghz que permite una velocidad máxima de 95Mbps a una distancia de 3metros y se pueden
                        conectarse equipos a una distancia de hasta 15metros en condiciones normales, sin embargo, la
                        distancia de cobertura varía según la cantidad y tipo de paredes, obstáculos e interferencia que
                        se encuentren en el entorno. El cliente conoce y acepta que la tecnología WiFi pierde potencia a
                        mayor distancia y por lo tanto se reducirá la velocidad efectiva a una mayor distancia de
                        conexión del equipo.</li>
                </ul>
            </div>

        </div>


        <div style="clear: both;"></div><br /><br /><br />
        <div class="labelBlock">CAMBIO DE RAZÓN SOCIAL</div>
            <div id="contenedor" class="col-width-100">





                <div id="row">
                    <div id="col" class="col-width-35">NOMBRE PERSONA NATURAL O JUDÍRICA (NUEVA):</div>
                    <div id="col" class="col-width-25 labelGris">
                        <span class="textPadding">$!nombrePersonaNueva</span>
                    </div>
                </div>
                <div id="row">
                    <div id="col" class="col-width-25"><b>REPRESENTANTE LEGAL:</b></div>
                    <div id="col" class="col-width-30 labelGris">
                        <span class="textPadding">$!nombreRepresentanteLegal</span>
                    </div>
                </div>
                <div id="row">
                    <div id="col" class="col-width-25"><b>NO. DE CONTRATO SIT(NUEVO):</b></div>
                    <div id="col" class="col-width-15 labelGris">
                        <span class="textPadding">$!numeroSit</span>
                    </div>

                    <div id="col" class="col-width-15"></div>
                    <div id="col" class="col-width-5" style="text-align : left; padding-right: 1px;"><b>C.I:</b></div>
                    <div class="box">$!isCedulaNuevo</div>

                    <div id="col" class="col-width-5" style="text-align : right; padding-right: 1px;"><b>RUC</b></div>
                    <div class="box">$!isRucNuevo</div>
                    <div id="col" class="col-width-10" style="text-align : right; padding-right: 1px;"><b>PASAPORTE</b>
                    </div>
                    <div class="box">$!isPasaporteNuevo</div>
                </div>
            </div>

            <div style="clear: both;"></div><br /><br /><br />
            <div class="labelBlock">TRASLADO <div class="box">$!isTraslado</div> REUBICACI&'||'Oacute;N <div class="box">
                    $!isReubicacion</div>

                <div id="contenedor" class="col-width-100">
                    <div id="row">
                        <div id="col" class="col-width-25">NUEVA DIRECCI&'||'Oacute;N:</div>
                        <div id="col" class="col-width-25 labelGris">
                            <span class="textPadding">$!direccionNueva</span>
                        </div>
                    </div>
                    <div id="row">
                        <div id="col" class="col-width-25"><b>REFERENCIA:</b></div>
                        <div id="col" class="col-width-30 labelGris">
                            <span class="textPadding">$!referenciaNueva</span>
                        </div>
                    </div>
                    <div id="row">
                        <div id="col" class="col-width-10"><b>CONTACTO:</b></div>
                        <div id="col" class="col-width-15 labelGris">
                            <span class="textPadding">$!contactoNuevo</span>
                        </div>

                        <div id="col" class="col-width-5"></div>
                        <div id="col" class="col-width-15"><b>TEL&'||'Eacute;FONOS:</b></div>
                        <div id="col" class="col-width-15 labelGris">
                            <span class="textPadding">$!telefonoNuevo</span>
                        </div>

                        <div id="col" class="col-width-5"></div>
                        <div id="col" class="col-width-10"><b>VALOR:</b></div>
                        <div id="col" class="col-width-15 labelGris">
                            <span class="textPadding">$!valorNuevo</span>
                        </div>

                    </div>
                </div>



                <div style="clear: both;"></div><br /><br /><br />
                <div class="labelBlock">OBSERVACIONES MEGADATOS/RECLAMOS CLIENTE <div class="box">$!isCambioRazonSocial
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
                                        <div id="colCell" class="col-width-50">MEGADATOS</div>
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
                                        <div id="colCell" class="col-width-50">Firma del Cliente</div>
                                        <div id="colCell" class="col-width-25"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

</body>

</html>');

dbms_output.put_line('adendumMegaDatos '||dbms_lob.getlength(bada));

UPDATE DB_FIRMAELECT.ADM_EMPRESA_PLANTILLA
SET HTML= bada
where COD_PLANTILLA = 'adendumMegaDatos';

INSERT INTO DB_FIRMAELECT.ADM_EMP_PLANT_CERT (ID_EMP_PLANT_CERT, PLANTILLA_ID, CERTIFICADO_ID, PROPIEDADES, TIPO, CODIGO)
VALUES (DB_FIRMAELECT.SEQ_ADM_EMP_PLANT_CERT.NEXTVAL, 
        (SELECT PLA.ID_EMPRESA_PLANTILLA
         FROM DB_FIRMAELECT.ADM_EMPRESA_PLANTILLA PLA
         WHERE PLA.COD_PLANTILLA = 'adendumMegaDatos' ),
        1,'{
                "llx": "390",
                "lly": "440",
                "urx": "570",
                "ury": "470",
                "pagina": "2",
                "textSignature": "",
                "modoPresentacion": "1"
            }',
          'cliente', 'FIRMA_ADEN_MD_CLIENTE'
);

INSERT INTO DB_FIRMAELECT.ADM_EMP_PLANT_CERT (ID_EMP_PLANT_CERT, PLANTILLA_ID, CERTIFICADO_ID, PROPIEDADES, TIPO, CODIGO)
VALUES (DB_FIRMAELECT.SEQ_ADM_EMP_PLANT_CERT.NEXTVAL, 
        (SELECT PLA.ID_EMPRESA_PLANTILLA
         FROM DB_FIRMAELECT.ADM_EMPRESA_PLANTILLA PLA
         WHERE PLA.COD_PLANTILLA = 'adendumMegaDatos' ),
        2,'{
                "llx": "70",
                "lly": "440",
                "urx":  "250",
                "ury": "470",
                "pagina": "2",
                "textSignature": "",
                "modoPresentacion": "1"
            }',
          'empresa', 'FIRMA_ADEN_MD_EMPRESA'
);


commit;
end;
