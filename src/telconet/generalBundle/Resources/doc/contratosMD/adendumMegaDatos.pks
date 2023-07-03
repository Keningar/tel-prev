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
        <script>
            function substitutePdfVariables() {
                var vars = {};
                var query_strings_from_url = document.location.search.substring(1).split("&");
                for (var query_string in query_strings_from_url) {
                    if (query_strings_from_url.hasOwnProperty(query_string)) {
                        var temp_var = query_strings_from_url[query_string].split("=", 2);
                        vars[temp_var[0]] = decodeURI(temp_var[1]);
                    }
                }
                var css_selector_classes = ["page", "frompage", "topage", "webpage", "section", "subsection", "date", "isodate", "time", "title", "doctitle", "sitepage", "sitepages"];
                for (var css_class in css_selector_classes) {
                    if (css_selector_classes.hasOwnProperty(css_class)) {
                        var element = document.getElementsByClassName(css_selector_classes[css_class]);
    
                        for (var j = 0; j < element.length; ++j) {
                            element[j].textContent = vars[css_selector_classes[css_class]];
                        }
                    }
                }
            }
        
        </script>
    </head>
    <body onload="substitutePdfVariables()">
        <!-- ================================ -->
        <!-- Logo Netlife y numero de contato -->
        <!-- ================================ -->

    <div id="contenedor" class="col-width-100" style="font-size:14px;">
        <table class="col-width-100">         
        <tr>     
            <td class="col-width-75" style="font-size:14px;"><b>ADENDUM SERVICIOS / PRODUCTOS ADICIONALES</b></td> 
            <td id="netlife" class="col-width-25" align="center" rowspan="2">
            <img src="http://images.telconet.net/others/telcos/logo_netlife.png" alt="log" title="NETLIFE" height="40"/>
            <div style="font-size:14px">Telf: 3920000</div>
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
                <div id="col" class="col-width-15">Persona contacto:</div>
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
                        <td class="line-height textCenter">$!subtotalUnico</td>
    
                        <td class="line-height textCenter">SUBTOTAL:</td>
                        <td class="line-height textCenter">$!subtotal</td>
                        <td class="line-height textCenter" style="border-bottom:1px white solid;"></td>
                    </tr>
                    <tr>
                        <td class="line-height textCenter" colspan="2" style="border-bottom:1px white solid;">IMPUESTOS:
                        </td>
                        <td class="line-height textCenter">$!impUnico</td>
                        <td class="line-height textCenter">IMPUESTOS:</td>
                        <td class="line-height textCenter">$!impuestos</td>
                        <td class="line-height textCenter" style="border-bottom:1px white solid;"></td>
                    </tr>
                    <tr>
                        <td class="line-height textCenter" colspan="2">TOTAL:</td>
                        <td class="line-height textCenter">$!totalUnico</td>
                        <td class="line-height textCenter">TOTAL:</td>
                        <td class="line-height textCenter">$total</td>
                        <td class="line-height textCenter"></td>
                    </tr>
                </table>
            </div>
            </div><div style="clear: both;"><br /></div>
            <div class="col-width-100" style="text-align: justify;">
            <div>NETLIFE puede modificar estas condiciones o las condiciones adicionales que se apliquen a un Servicio o Producto con el fin, por ejemplo, de reflejar cambios legislativos, sustituci&oacute;n y/o mejoras en los Servicios prestados. La contrataci&oacute;n de nuestros Servicios implica la aceptaci&oacute;n de las condiciones descritas a este documento por lo que el cliente entiende y acepta que las ha leído detenidamente y conoce todos sus detalles.</div>
        
                <br />
                <div> <b>INFORMACION Y CONDICIONES ADICIONALES: </b> </div>
                <br />
                <div class="clausulas">
                    <ul>
                        <li>El cliente conoce, acepta las condiciones aqu&iacute; descritas, por lo cu&aacute;l suscribe el presente documento de contrataci&oacute;n de servicios adicionales, el cual forma parte del contrato de adhesión bajo la misma forma de pago suscrita entre el cliente y NETLIFE.</li>
                        <br />
                        <li>El cliente conoce, entiende y acepta que ha recibido toda la informaci&oacute;n referente al servicio(s) /producto(s) adicional(es) contratado(s)  y que est&aacute; de acuerdo con todos los items descritos en el presente documento. El cliente conoce, entiende y acepta que el servicio contratado con NETLIFE NO incluye cableado interno o configuraci&oacute;n de la red local del cliente e incluye condiciones de permanencia m&iacute;nima en caso de recibir promociones.</li>
                        <br />
                        <li>Los servicios adicionales generan una facturación proporcional al momento de la contratación y luego ser&aacute; de forma recurrente. El servicio adicional estar&aacute; activo mientras el cliente est&eacute; al d&iacute;a en pagos, caso contrario no podr&aacute; acceder al mismo por estar suspendido.</li>  
                    </ul>
                </div>

                <br /><br />
                {{listaTerminosProductos}}

                <br /><br />
                <div>
                El cliente con la sola suscripci&oacute;n voluntaria del presente Adendum confirma que ha le&iacute;do y conoce las condiciones de uso de los equipos y servicios descritos a ser contratados. La informaci&oacute;n proporcionada en el presente documento, el cliente autoriza a Megadatos para uso y tratamiento  acorde a la normativa legal vigente a fin al contrato de adhesi&oacute;n.
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
</html>