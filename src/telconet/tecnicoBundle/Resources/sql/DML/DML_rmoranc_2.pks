--Actualizando Plantillas acta de soporte TN	
SET SERVEROUTPUT ON 200000;
declare
    bada clob:='<!DOCTYPE html>';
begin

DBMS_LOB.APPEND(bada, '<html>
   <head>
      <meta content="text/html; charset=UTF-8" http-equiv="content-type">
      <style type="text/css">@import url("https://themes.googleusercontent.com/fonts/css?kit=fpjTOVmNbO4Lz34iLyptLVumN3ATOVc2BoeDKcwJhFTljiSzuFEcjsip7pjNdcnF");ol{margin:0;padding:0}table td,table th{padding:0}.c18{border-right-style:solid;padding:0pt 5.4pt 0pt 5.4pt;border-bottom-color:#000000;border-top-width:1pt;border-right-width:1pt;border-left-color:#000000;vertical-align:top;border-right-color:#000000;border-left-width:1pt;border-top-style:solid;border-left-style:solid;border-bottom-width:1pt;width:228.4pt;border-top-color:#000000;border-bottom-style:solid}.c66{border-right-style:solid;padding:0pt 5.8pt 0pt 5.8pt;border-bottom-color:#000000;border-top-width:1pt;border-right-width:1pt;border-left-color:#000000;vertical-align:top;border-right-color:#000000;border-left-width:1pt;border-top-style:solid;border-left-style:solid;border-bottom-width:0pt;width:201pt;border-top-color:#000000;border-bottom-style:solid}.c10{border-right-style:solid;padding:0pt 5.8pt 0pt 5.8pt;border-bottom-color:#000000;border-top-width:0pt;border-right-width:1pt;border-left-color:#000000;vertical-align:top;border-right-color:#000000;border-left-width:1pt;border-top-style:solid;border-left-style:solid;border-bottom-width:1pt;width:129pt;border-top-color:#000000;border-bottom-style:solid}.c43{border-right-style:solid;padding:0pt 5.4pt 0pt 5.4pt;border-bottom-color:#000000;border-top-width:1pt;border-right-width:1pt;border-left-color:#000000;vertical-align:top;border-right-color:#000000;border-left-width:1pt;border-top-style:solid;border-left-style:solid;border-bottom-width:1pt;width:42.5pt;border-top-color:#000000;border-bottom-style:solid}.c27{border-right-style:solid;padding:0pt 3.5pt 0pt 3.5pt;border-bottom-color:#000000;border-top-width:1pt;border-right-width:1pt;border-left-color:#000000;vertical-align:top;border-right-color:#000000;border-left-width:1pt;border-top-style:solid;border-left-style:solid;border-bottom-width:1pt;width:157.2pt;border-top-color:#000000;border-bottom-style:solid}.c30{border-right-style:solid;padding:5pt 5pt 5pt 5pt;border-bottom-color:#000000;border-top-width:1pt;border-right-width:1pt;border-left-color:#000000;vertical-align:top;border-right-color:#000000;border-left-width:1pt;border-top-style:solid;border-left-style:solid;border-bottom-width:1pt;width:457pt;border-top-color:#000000;border-bottom-style:solid}.c2{border-right-style:solid;padding:0pt 5.4pt 0pt 5.4pt;border-bottom-color:#000000;border-top-width:1pt;border-right-width:1pt;border-left-color:#000000;vertical-align:top;border-right-color:#000000;border-left-width:1pt;border-top-style:solid;border-left-style:solid;border-bottom-width:1pt;width:239.3pt;border-top-color:#000000;border-bottom-style:solid}.c29{border-right-style:solid;padding:0pt 5.4pt 0pt 5.4pt;border-bottom-color:#000000;border-top-width:1pt;border-right-width:1pt;border-left-color:#000000;vertical-align:top;border-right-color:#000000;border-left-width:1pt;border-top-style:solid;border-left-style:solid;border-bottom-width:1pt;width:197.8pt;border-top-color:#000000;border-bottom-style:solid}.c22{border-right-style:solid;padding:0pt 3.5pt 0pt 3.5pt;border-bottom-color:#000000;border-top-width:1pt;border-right-width:1pt;border-left-color:#000000;vertical-align:top;border-right-color:#000000;border-left-width:1pt;border-top-style:solid;border-left-style:solid;border-bottom-width:1pt;width:316.9pt;border-top-color:#000000;border-bottom-style:solid}.c24{border-right-style:solid;padding:0pt 5.8pt 0pt 5.8pt;border-bottom-color:#000000;border-top-width:0pt;border-right-width:1pt;border-left-color:#000000;vertical-align:top;border-right-color:#000000;border-left-width:1pt;border-top-style:solid;border-left-style:solid;border-bottom-width:0pt;width:121pt;border-top-color:#000000;border-bottom-style:solid}.c47{border-right-style:solid;padding:0pt 5.4pt 0pt 5.4pt;border-bottom-color:#000000;border-top-width:1pt;border-right-width:1pt;border-left-color:#000000;vertical-align:top;border-right-color:#000000;border-left-width:1pt;border-top-style:solid;border-left-style:solid;border-bottom-width:1pt;width:44.2pt;border-top-color:#000000;border-bottom-style:solid}.c63{border-right-style:solid;padding:0pt 5.8pt 0pt 5.8pt;border-bottom-color:#000000;border-top-width:1pt;border-right-width:1pt;border-left-color:#000000;vertical-align:top;border-right-color:#000000;border-left-width:1pt;border-top-style:solid;border-left-style:solid;border-bottom-width:0pt;width:121pt;border-top-color:#000000;border-bottom-style:solid}.c46{border-right-style:solid;padding:0pt 5.8pt 0pt 5.8pt;border-bottom-color:#000000;border-top-width:1pt;border-right-width:1pt;border-left-color:#000000;vertical-align:top;border-right-color:#000000;border-left-width:1pt;border-top-style:solid;border-left-style:solid;border-bottom-width:0pt;width:129pt;border-top-color:#000000;border-bottom-style:solid}.c41{border-right-style:solid;padding:0pt 5.8pt 0pt 5.8pt;border-bottom-color:#000000;border-top-width:0pt;border-right-width:1pt;border-left-color:#000000;vertical-align:top;border-right-color:#000000;border-left-width:1pt;border-top-style:solid;border-left-style:solid;border-bottom-width:1pt;width:121pt;border-top-color:#000000;border-bottom-style:solid}.c48{border-right-style:solid;padding:0pt 5.8pt 0pt 5.8pt;border-bottom-color:#000000;border-top-width:0pt;border-right-width:1pt;border-left-color:#000000;vertical-align:top;border-right-color:#000000;border-left-width:1pt;border-top-style:solid;border-left-style:solid;border-bottom-width:0pt;width:129pt;border-top-color:#000000;border-bottom-style:solid}.c57{border-right-style:solid;padding:0pt 5.8pt 0pt 5.8pt;border-bottom-color:#000000;border-top-width:0pt;border-right-width:1pt;border-left-color:#000000;vertical-align:top;border-right-color:#000000;border-left-width:1pt;border-top-style:solid;border-left-style:solid;border-bottom-width:1pt;width:201pt;border-top-color:#000000;border-bottom-style:solid}.c37{border-right-style:solid;padding:0pt 3.5pt 0pt 3.5pt;border-bottom-color:#000000;border-top-width:1pt;border-right-width:1pt;border-left-color:#000000;vertical-align:top;border-right-color:#000000;border-left-width:1pt;border-top-style:solid;border-left-style:solid;border-bottom-width:1pt;width:151.5pt;border-top-color:#000000;border-bottom-style:solid}.c16{border-right-style:solid;padding:0pt 5.4pt 0pt 5.4pt;border-bottom-color:#000000;border-top-width:1pt;border-right-width:1pt;border-left-color:#000000;vertical-align:top;border-right-color:#000000;border-left-width:1pt;border-top-style:solid;border-left-style:solid;border-bottom-width:1pt;width:177.2pt;border-top-color:#000000;border-bottom-style:solid}.c40{border-right-style:solid;padding:0pt 5.8pt 0pt 5.8pt;border-bottom-color:#000000;border-top-width:0pt;border-right-width:1pt;border-left-color:#000000;vertical-align:top;border-right-color:#000000;border-left-width:1pt;border-top-style:solid;border-left-style:solid;border-bottom-width:0pt;width:201pt;border-top-color:#000000;border-bottom-style:solid}.c56{border-right-style:solid;padding:0pt 3.5pt 0pt 3.5pt;border-bottom-color:#000000;border-top-width:1pt;border-right-width:1pt;border-left-color:#000000;vertical-align:top;border-right-color:#000000;border-left-width:1pt;border-top-style:solid;border-left-style:solid;border-bottom-width:1pt;width:159.8pt;border-top-color:#000000;border-bottom-style:solid}.c14{border-right-style:solid;padding:0pt 3.5pt 0pt 3.5pt;border-bottom-color:#000000;border-top-width:1pt;border-right-width:1pt;border-left-color:#000000;vertical-align:middle;border-right-color:#000000;border-left-width:1pt;border-top-style:solid;border-left-style:solid;border-bottom-width:1pt;width:151.5pt;border-top-color:#000000;border-bottom-style:solid}.c5{color:#000000;font-weight:400;text-decoration:none;vertical-align:baseline;font-size:11pt;font-family:"Calibri";font-style:normal}.c1{color:#000000;font-weight:700;text-decoration:none;vertical-align:baseline;font-size:15pt;font-family:"Times New Roman";font-style:normal}.c0{color:#000000;font-weight:400;text-decoration:none;vertical-align:baseline;font-size:11pt;font-family:"Arial";font-style:normal}.c60{color:#000000;font-weight:400;text-decoration:none;vertical-align:baseline;font-size:9.5pt;font-family:"Times New Roman";font-style:normal}.c53{color:#000000;font-weight:700;text-decoration:none;vertical-align:baseline;font-size:15pt;font-family:"Cambria";font-style:normal}.c36{color:#000000;font-weight:400;text-decoration:none;vertical-align:baseline;font-size:10.5pt;font-family:"Times New Roman";font-style:normal}.c34{color:#000000;font-weight:700;text-decoration:none;vertical-align:baseline;font-size:12pt;font-family:"Arial";font-style:normal}.c13{color:#000000;font-weight:400;text-decoration:none;vertical-align:baseline;font-size:9pt;font-family:"Times New Roman";font-style:normal}.c9{margin-left:6pt;padding-top:0pt;padding-bottom:0pt;line-height:0.9958333333333332;text-align:left;height:11pt}.c12{margin-left:6pt;padding-top:0pt;padding-bottom:0pt;line-height:0.9958333333333332;text-align:justify;height:11pt}.c44{margin-left:6pt;padding-top:0pt;padding-bottom:0pt;line-height:0.9958333333333332;text-align:justify}.c65{padding-top:12pt;padding-bottom:3pt;line-height:1.1500000000000001;page-break-after:avoid;text-align:left}.c3{padding-top:0pt;padding-bottom:0pt;line-height:1.1500000000000001;text-align:left;height:11pt}.c49{margin-left:6pt;padding-top:0pt;padding-bottom:0pt;line-height:1.5;text-align:left}.c11{margin-left:6pt;padding-top:0pt;padding-bottom:0pt;line-height:0.9958333333333332;text-align:left}.c21{padding-top:0pt;padding-bottom:0pt;line-height:1.15;text-align:left}.c4{padding-top:0pt;padding-bottom:0pt;line-height:1.0;text-align:left}.c31{vertical-align:baseline;font-size:10pt;font-family:"Arial";font-weight:700}.c45{color:#000000;text-decoration:underline;font-size:11pt;font-style:normal}.c51{color:#000000;text-decoration:none;font-size:9pt;font-style:normal}.c8{padding-top:0pt;padding-bottom:0pt;line-height:1.0;text-align:center}.c6{padding-top:0pt;padding-bottom:46pt;line-height:1.1500000000000001;text-align:center}.c59{padding-top:0pt;padding-bottom:0pt;line-height:0.9958333333333332;text-align:left}.c26{margin-left:-5.2pt;border-spacing:0;border-collapse:collapse;margin-right:auto}.c58{padding-top:0pt;padding-bottom:0pt;line-height:1.1500000000000001;text-align:center}.c50{border-spacing:0;border-collapse:collapse;margin-right:auto}.c62{margin-left:-5.4pt;border-spacing:0;border-collapse:collapse;margin-right:auto}.c15{padding-top:0pt;padding-bottom:10pt;line-height:0.9958333333333332;text-align:justify}.c28{margin-left:0.6pt;border-spacing:0;border-collapse:collapse;margin-right:auto}.c33{vertical-align:baseline;font-size:9pt;font-family:"Arial";font-weight:700}.c17{vertical-align:baseline;font-family:"Arial";font-weight:400}.c64{background-color:#ffffff;max-width:457pt;padding:26.1pt 59pt 22.5pt 79pt}.c52{vertical-align:baseline;font-family:"Arial";font-weight:700}.c61{font-family:"Arial";font-weight:700}.c39{height:4pt}.c54{height:22pt}.c42{height:23pt}.c32{margin-left:3pt}.c19{height:11pt}.c35{height:0pt}.c23{height:1pt}.c55{font-size:12pt}.c7{height:15pt}.title{padding-top:24pt;color:#000000;font-weight:700;font-size:36pt;padding-bottom:6pt;font-family:"Calibri";line-height:1.1500000000000001;page-break-after:avoid;text-align:left}.subtitle{padding-top:18pt;color:#666666;font-size:24pt;padding-bottom:4pt;font-family:"Georgia";line-height:1.1500000000000001;page-break-after:avoid;font-style:italic;text-align:left}li{color:#000000;font-size:11pt;font-family:"Calibri"}p{margin:0;color:#000000;font-size:11pt;font-family:"Calibri"}h1{padding-top:24pt;color:#000000;font-weight:700;font-size:24pt;padding-bottom:6pt;font-family:"Calibri";line-height:1.1500000000000001;page-break-after:avoid;text-align:left}h2{padding-top:18pt;color:#000000;font-weight:700;font-size:18pt;padding-bottom:4pt;font-family:"Calibri";line-height:1.1500000000000001;page-break-after:avoid;text-align:left}h3{padding-top:14pt;color:#000000;font-weight:700;font-size:14pt;padding-bottom:4pt;font-family:"Calibri";line-height:1.1500000000000001;page-break-after:avoid;text-align:left}h4{padding-top:12pt;color:#000000;font-weight:700;font-size:12pt;padding-bottom:2pt;font-family:"Calibri";line-height:1.1500000000000001;page-break-after:avoid;text-align:left}h5{padding-top:11pt;color:#000000;font-weight:700;font-size:11pt;padding-bottom:2pt;font-family:"Calibri";line-height:1.1500000000000001;page-break-after:avoid;text-align:left}h6{padding-top:10pt;color:#000000;font-weight:700;font-size:10pt;padding-bottom:2pt;font-family:"Calibri";line-height:1.1500000000000001;page-break-after:avoid;text-align:left}

            .contenedor_firma {
                height:40px;
            }

            .firma_left {
                width: 200px;
                float: left;
                border-top: 1px solid #000;
                text-align: center;
                margin-left:20px;
            }

            .firma_right {
                width: 200px;
                float: right;
                border-top: 1px solid #000;
                text-align: center;
                margin-right:20px;
            }

            .imagen_firma_left {
                width: 200px;
                float: left;
                text-align: center;
                margin-left:20px;
            }

            .imagen_firma_right {
                width: 200px;
                float: right;
                text-align: center;
                margin-right:20px;
            }
            label, .labelGris
            { 
                background: #E6E6E6;
            }
            label, .labelGrisCelda
            { 
                background: #E6E6E6;
                border: 1px solid black;
            }

            #equipos, #equipos th
            {
                border: 1px solid black;
            }
            #equipos th
            {
                background: #00579a;
            }
      </style>
   </head>
   <body class="c64">
      <div>
         <p class="c4 c19"><span class="c38 c17"></span></p>
         <a id="t.66ab78d3461b083805d44b46e7c04547b0df0cae"></a><a id="t.4"></a>
         <table class="c26">
            <tbody>
               <tr class="c42">
                  <td class="c63" colspan="1" rowspan="1">
                     <p class="c8"><span style="overflow: hidden; display: inline-block; margin: 0.00px 0.00px; border: 0.00px solid #000000; transform: rotate(0.00rad) translateZ(0px); -webkit-transform: rotate(0.00rad) translateZ(0px); width: 116.00px; height: 48.00px;"><img alt="" src="{{imagenCabecera}}" style="width: 116.00px; height: 48.00px; margin-left: -0.00px; margin-top: -0.00px; transform: rotate(0.00rad) translateZ(0px); -webkit-transform: rotate(0.00rad) translateZ(0px);" title=""></span></p>
                  </td>
                  <td class="c66" colspan="1" rowspan="1">
                     <p class="c58"><span class="c31">ACTA DE ENTREGA DE ÚLTIMA MILLA O VISITA TÉCNICA</span></p>
                  </td>
                  <td class="c46" colspan="1" rowspan="1">
                     <p class="c20 c32"><span class="c33">CODIGO:  FOR OPU 05</span></p>
                     <p class="c20 c32"><span class="c33">Ver. 03 03 17</span></p>
                  </td>
               </tr>
               <tr class="c19">
                  <td class="c24" colspan="1" rowspan="1">
                     <p class="c4 c19"><span class="c60"></span></p>
                  </td>
                  <td class="c40" colspan="1" rowspan="1">
                     <p class="c58"><span class="c31">TELCONET</span></p>
                  </td>
                  <td class="c48" colspan="1" rowspan="1">
                     <p class="c3 c32"><span class="c13"></span></p>
                  </td>
               </tr>
               <tr class="c39">
                  <td class="c41" colspan="1" rowspan="1">
                     <p class="c4 c19"><span class="c36"></span></p>
                  </td>
                  <td class="c57" colspan="1" rowspan="1">
                     <p class="c4 c19"><span class="c36"></span></p>
                  </td>
                  <td class="c10" colspan="1" rowspan="1">
                     <p class="c4 c19"><span class="c36"></span></p>
                  </td>
               </tr>
            </tbody>
         </table>
         <p class="c4 c19"><span class="c5"></span></p>
      </div>
      <p class="c65"><span class="c53">INFORME DE TRABAJOS DIARIOS</span></p>
      <p class="c4"><span class="c52 c55">Datos</span></p>
      <p class="c4 c19"><span class="c13"></span></p>
      <a id="t.42050c255a0208294a210215b25d85f753801513"></a><a id="t.0"></a>
      <table class="c62">
         <tbody>
            <tr class="c23">
               <td class="c2" colspan="1" rowspan="1">
                  <p class="c20"><span class="c0">FECHA:</span></p>
               </td>
               <td class="c18" colspan="1" rowspan="1">
                  <p><span class="c13">{{ fecha }}</span></p>
               </td>
            </tr>
            <tr class="c23">
               <td class="c2" colspan="1" rowspan="1">
                  <p class="c20"><span class="c0">CLIENTE:</span></p>
               </td>
               {% if datosCliente.NOMBRES|length > 2 %}
                   <td class="c18" colspan="1" rowspan="1">
                       <p><span class="c13">{{ datosCliente.NOMBRES }}</span></p>
                   </td>
               {% else %}
                   <td class="c18" colspan="1" rowspan="1">
                       <p><span class="c13">{{ datosCliente.RAZON_SOCIAL }}</span></p>
                   </td>
               {% endif %}
            </tr>
            <tr class="c23">
               <td class="c2" colspan="1" rowspan="1">
                  <p class="c20"><span class="c0">LOGIN:</span></p>
               </td>
               <td class="c18" colspan="1" rowspan="1">
                  <p><span class="c13">{{ datosCliente['''||'LOGIN'||'''] }}</span></p>
               </td>
            </tr>
            <tr class="c23">
               <td class="c2" colspan="1" rowspan="1">
                  <p class="c20"><span class="c0">DIRECCIÓN:</span></p>
               </td>
               <td class="c18" colspan="1" rowspan="1">
                  <p><span class="c13">{{ datosCliente['''||'DIRECCION'||'''] }}</span></p>
               </td>
            </tr>
            <tr class="c23">
               <td class="c2" colspan="1" rowspan="1">
                  <p class="c20"><span class="c0">COORDENADAS:</span></p>
               </td>
               <td class="c18" colspan="1" rowspan="1">
                  <p><span class="c13">{{ datosCliente['''||'LONGITUD'||'''] }},{{ datosCliente['''||'LATITUD'||'''] }}</span></p>
               </td>
            </tr>
            <tr class="c23">
               <td class="c2" colspan="1" rowspan="1">
                  <p class="c20"><span class="c0">NOMBRE DEL CONTACTO:</span></p>
               </td>
               <td class="c18" colspan="1" rowspan="1">
                  <p><span class="c13">
                    
                    {% if contactoCliente %}
                        {{ contactoCliente['''||'NOMBRE_CONTACTO'||'''] }}
                    {% else %}
                        NA
                    {% endif %}

                  </span></p>
               </td>
            </tr>
            <tr class="c23">
               <td class="c2" colspan="1" rowspan="1">
                  <p class="c20"><span class="c0">FORMA DE CONTACTO:</span></p>
               </td>
               <td class="c18" colspan="1" rowspan="1">
                  <p><span class="c13">
                    
                     {% for contacto in formaContactoCliente['''||'registros'||'''] %}
                             <b>{{ contacto['''||'descripcionFormaContacto'||'''] }}:</b>{{ contacto['''||'valor'||'''] }}
                     {% endfor %}                    

                  </span></p>
               </td>
            </tr>
         </tbody>
      </table>


        <!-- --------------------------------------------------------------------------------------------------------------- -->

      <p class="c3"><span class="c13"></span></p>
      <p class="c44"><span class="c52 c55">Equipos</span></p>
      <p class="c12"><span class="c17 c45"></span></p>
        <table id="equipos" width="100%" class="box-section-content" style="font-size:11px; text-align: center; border-collapse: collapse;">
            <th>Tipo</th>
            <th>Modelo</th>
            <th>Marca</th>
            <th>Serie</th>
            <th>Mac</th>
           {% for elemento in equiposEntregado %}
              <tr>
                  <td style="width:20%" class ="labelGrisCelda">{{ elemento['''||'tipo'||'''] }}</td>
                  <td style="width:20%" class ="labelGrisCelda">{{ elemento['''||'modelo'||'''] }}</td>
                  <td style="width:20%" class ="labelGrisCelda">{{ elemento['''||'marca'||'''] }}</td>
                  <td style="width:20%" class ="labelGrisCelda">{{ elemento['''||'serie'||'''] }}</td>
                  <td style="width:20%" class ="labelGrisCelda">{{ elemento['''||'mac'||'''] }}</td>
              </tr>
           {% endfor %}
        </table>
      
        <!-- --------------------------------------------------------------------------------------------------------------- -->
      <p class="c3"><span class="c13"></span></p>
      <p class="c44"><span class="c52 c55">Facturable</span></p>
      <p class="c12"><span class="c17 c45"></span></p>
      <table id="equipos" width="100%" class="box-section-content" style="font-size:11px; text-align: center; border-collapse: collapse;">
            <tr>
               {% if facturable == true %}
                   <td style="width: 50%" class ="labelGris">SI</td>
               {% else %}
                   <td style="width: 50%" class ="labelGris">NO</td>
               {% endif %}
            </tr>
      </table>
        <!-- --------------------------------------------------------------------------------------------------------------- -->
      <p class="c3"><span class="c13"></span></p>
      <p class="c44"><span class="c52 c55">Servicios</span></p>
      <p class="c12"><span class="c17 c45"></span></p>
      <a id="t.ec476c139a0af8ba823b60724841db783ed399b5"></a><a id="t.3"></a>
      <table class="c28">
         <tbody>
       {% set limiteColumna = 0 %}

       {% for actaEntrega in cuerpo %}
       {% if actaEntrega.pregunta == "Servicios Contratados" %}
              {% if limiteColumna == 0 %}
               <tr>
              {% endif %}
              
                  <td class="c29" colspan="1" rowspan="1">
                  <p class="c15"><span class="c0">{{ actaEntrega.respuesta }}</span></p>
                  </td>
                   {% if actaEntrega.flag == "true" %}
                     <td class="c43" colspan="1" rowspan="1">
                        <p class="c15 c19"><span class="c0">&#10004;</span></p>
                     </td>
                   {% else %}
                     <td class="c43" colspan="1" rowspan="1">
                        <p class="c15 c19"><span class="c0"></span></p>
                     </td>
                   {% endif %}
              
              {% set limiteColumna = limiteColumna + 2 %}

              {% if limiteColumna == 4 %}
                  </tr>
                  {% set limiteColumna = 0 %}
              {% endif %}
       {% endif %}       
       {% endfor %}

       {% if limiteColumna > 0 %}
           </tr>
       {% endif %}

         </tbody>
      </table>
        <!-- --------------------------------------------------------------------------------------------------------------- -->
      <p class="c12"><span class="c45 c17"></span></p>
      <p class="c44"><span class="c52">NOVEDADES SOBRE LA INSTALACION Y LOS BIENES DE PROPIEDAD DEL CLIENTE</span></p>
      <p class="c9"><span class="c0"></span></p>

      {% for actaEntrega in cuerpo %}
          {% if actaEntrega.pregunta != "Servicios Contratados" and actaEntrega.flag == "true" %}
          <p class="c11"><span class="c0"><b>{% autoescape %}{{ actaEntrega.pregunta|raw }}{% endautoescape %}</b></span></p>
              <p class="c9"><span class="c17"></span></p>
              <p class="c11"><span class="c0">{{ actaEntrega.respuesta }}</span></p>
              <p class="c9"><span class="c17"></span></p>
           {% endif %}
      {% endfor %}
      <p class="c9"><span class="c0"></span></p>

	  
	  <div style="margin-top: 20px;">
         <hr>
		 <p class="c6"><span>Esta acta es evidencia de la visita técnica realizada.</span></p>
		 
      </div>
	  
        <!-- --------------------------------------------------------------------------------------------------------------- -->

        <table style="font-size:11px; text-align: center; border-collapse: collapse;">
            <tr>
                <td style="width: 50%"><img src="{{firmaEmpleado}}" alt="firma" width="250"/></td>
                <td style="width: 50%"><img src="{{firmaCliente}}" alt="firma" width="250"/></td>
            </tr>
            <tr>
                {% if firmaEmpresa == true %}
                    <td style="width: 50%"><span><hr style="width: 70%;"/>Ing. Hugo Proaño</span></td>
                {% else %}
                    <td style="width: 50%"><span><hr style="width: 70%;"/>Firma del Técnico Responsable</span></td>
                {% endif %}
                
                <td style="width: 50%"><span><hr style="width: 70%;"/>Firma Cliente</span></td>
            </tr>
        </table>

      <div style="margin-top: 150px;">
         <hr>
         <p class="c6"><span>©2017 Grupo Telconet-Documento confidencial. Prohibida su distribución sin previa autorización</span></p>
      </div>
   </body>
</html>');

dbms_output.put_line('The length of the manipulated LOB is '||dbms_lob.getlength(bada));

UPDATE 
	DB_COMUNICACION.ADMI_PLANTILLA 
SET 
	PLANTILLA = bada
where CODIGO = 'ACT-ENT-TN-VIS';
commit;
end;
/
