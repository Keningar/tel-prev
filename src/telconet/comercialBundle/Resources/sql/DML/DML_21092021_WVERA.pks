--Actualizando Plantillas ACT-ENT-TN-INS	
SET SERVEROUTPUT ON 200000;
declare
    plantillaHtml
 clob:='<!DOCTYPE html>';
begin

DBMS_LOB.APPEND(plantillaHtml, 
'
<!DOCTYPE html><html>
 <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <style>
        figure {
            background-color: #ffffff;
            max-width: 510pt;
        }

        td,
        th {
            border: 1px solid rgb(24, 24, 24);
            text-align: left;
            max-width: 510pt;
        }

        table {
            max-width: 510pt;
            height: 100vh;
            width: 100vw;
            position: absolute;
            margin: auto;
            top: 0;
            left: 0;
        }

        titulos {
            text-align: center;
            font-size: 22px;
            color: rgb(5, 8, 17);
        }

        subTitulos {
            text-align: center;
            font-size: 20px;
            color: rgb(5, 8, 17);
        }

        contenido {
            text-align: center;
            font-size: 15px;
            color: black;
        }

        contenidoTabla {
            text-align: center;
            font-size: 18px;
            color: rgb(42, 42, 42);
        }

        firma {
            margin: 1px;
            border: 1px solid rgba(0, 0, 0, .1);
            height: 150px;
            width: 150px;
        }

        figcaption {
            padding: 1em 0;
        }


        th,
        td {
            padding: 10px;
        }
    </style>
</head>
<figure class="figure" colspan="100%">
    <table class="table">
        <tbody>
            <tr>
                <td style="height:35pt" colspan="2">
                    <center>
                        <img src="{{imagenCabecera}}">
                    </center>
                </td>
                <td colspan="3">
                    <titulos>
                        <center>ACTA DE ENTREGA DE &Uacute;LTIMA MILLA Y VISITA T&Eacute;CNICA</center>
                    </titulos>
                </td>
                <td colspan="3">
                    <subTitulos>
                        <strong>CODIGO: FOR OPU 05&nbsp;
                            <br>Ver: 3 (08/06/2021)
                    </subTitulos>
                </td>
            </tr>
            <tr>
                <td style="height:32pt;" colspan="7">
                    <h2 class="titulos">
                        <subtitulos>
                            <strong>INFORME DE TRABAJOS DIARIOS</strong>
                            <br>
                            <strong>Datos</strong>
                        </subtitulos>
                    </h2>
                </td>
            </tr>
            <tr>
                <td class="subTitulos" style="height:12,75pt;" colspan="3">
                    <contenido>
                        <strong>FECHA</strong>:
                    </contenido>
                </td>
                <td class="subTitulos" colspan="5">
                    <contenido>{{ fecha }}</contenido>
                </td>
            </tr>
            <tr>
                <td style="height:12,75pt; width:195,00pt;" colspan="3">
                    <contenido>
                        <strong>CLIENTE</strong>:
                    </contenido>
                </td>
                {% if datosCliente.NOMBRES|length > 1 %}
                  <td colspan="5">
                      <contenido>{{ datosCliente.NOMBRES }}</contenido>
                  </td>
                {% else %}
                  <td colspan="5">
                      <contenido>{{ datosCliente.RAZON_SOCIAL }}</contenido>
                  </td>
                {% endif %}

            </tr>
            <tr>
                <td style="height:12,75pt;width:195,00pt;" colspan="3">
                    <contenido>
                        <strong>LOGIN</strong>
                    </contenido>:

                </td>
                <td colspan="5">
                    <contenido>{{ datosCliente['LOGIN'] }}</contenido>
                </td>
            </tr>
            <tr>
                <td style="height:12,75pt;width:195,00pt;" colspan="3">
                    <contenido>
                        <strong>DIRECCI&Oacute;N</strong>:
                    </contenido>
                </td>
                <td colspan="5">
                    <contenido>{{ datosCliente['DIRECCION'] }}</contenido>
                </td>
            </tr>
            <tr>
                <td style="height:12,75pt;width:195,00pt;" colspan="3">
                    <contenido>
                        <strong>COORDENADAS</strong>:
                    </contenido>
                </td>
                <td colspan="5">
                    <contenido>{{ datosCliente['LONGITUD'] }},{{ datosCliente['LATITUD'] }}</contenido>
                </td>
            </tr>
            <tr>
                <td style="height:12,75pt;width:195,00pt;" colspan="3">
                    <contenido>
                        <strong>NOMBRE DEL CONTACTO</strong>:
                    </contenido>
                </td>
                <td colspan="5">
                    <contenido>
                        {% if contactoCliente %}
                        {{ contactoCliente['NOMBRE_CONTACTO'] }}
                        {% else %}
                        NA
                        {% endif %}
                    </contenido>
                </td>
            </tr>
            <tr>
                <td style="height:12,75pt;width:195,00pt;" colspan="3">
                    <contenido>
                        <strong>FORMA DE CONTACTO</strong>:
                    </contenido>
                </td>
                <td colspan="5">
                    <contenido>
                        {% for contacto in formaContactoCliente['registros'] %}

                        <b>{{ contacto['descripcionFormaContacto'] }}:</b>{{ contacto['valor'] }}
                        {% endfor %}

                    </contenido>
                </td>
            </tr>
            <tr>
                <td style="height:13,50pt;width:379,50pt;" colspan="7">
                    <h3>
                        <titulos>Equipos</titulos>
                    </h3>
                </td>
            </tr>
            <tr>
                <td style="height:13,50pt;width:65,25pt; background-color:#015191;  text-align: center;" colspan="2">
                    <contenido> 
                        <strong>Tipo</strong>
                    </contenido>
                </td>
                <td style="height:13,50pt;width:65,25pt; background-color:#015191; text-align: center;" colspan="1">
                   <contenido> 
                   		<strong>Modelo</strong>
                   </contenido>
                </td>
                <td style="height:13,50pt;width:65,25pt; background-color:#015191; text-align: center;" colspan="1">
                    <contenido>
                    	<strong>Marca</strong>
                    </contenido>
                </td>
                <td style="height:13,50pt;width:65,25pt; background-color:#015191; text-align: center;" colspan="1">
                    <contenido>
                        <strong>Serie</strong>
                    </contenido>
                </td>
                <td style="height:13,50pt;width:65,25pt; background-color:#015191; text-align: center;" colspan="2">
                    <contenido>
                    	<strong>Mac</strong>
                    </contenido>
                </td>
            </tr>
            {% for elemento in equiposEntregado %}

            <tr>
                <td style="height:13,50pt;" colspan="2">{{ elemento['tipo'] }}</td>
                <td colspan="1">
                    <contenidoTabla>{{ elemento['modelo'] }}</contenidoTabla>
                </td>
                <td colspan="1">
                    <contenidoTabla>{{ elemento['marca'] }}</contenidoTabla>
                </td>
                <td colspan="1">
                    <contenidoTabla>{{ elemento['serie'] }}</contenidoTabla>
                </td>
                <td colspan="2">
                    <contenidoTabla>{{ elemento['mac'] }}</contenidoTabla>
                </td>
            </tr>
            {% endfor %}

            <tr>
                <td style="height:13,50pt;width:379,50pt;" colspan="7">
                    <h3>
                        <titulos>Servicios</titulos>
                    </h3>
                </td>
            </tr>
            {% set limiteColumna = 0 %}

            {% for actaEntrega in cuerpo %}
            {% if actaEntrega.pregunta == "Servicios Contratados" %}
            {% if limiteColumna == 0 %}

            <tr>
                {% endif %}


                <td colspan="2" rowspan="1">
                    <p class="">
                        <span class="">{{ actaEntrega.respuesta }}</span>
                    </p>
                </td>
                {% if actaEntrega.flag == "true" %}

                <td class="" colspan="1" rowspan="1">
                    <p>
                        <center>
                            <strong>X</strong>
                        </center>
                    </p>
                </td>
                {% else %}

                <td class="" colspan="1" rowspan="1">
                    <p class="">
                        <span class=""></span>
                    </p>
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
            {% endif %}

            <tr>
                <td style="height:17,10pt;width:65,25pt;" colspan="7">&nbsp;</td>
            </tr>
            <tr>
                <td style="height:38,45pt;width:379,50pt;" colspan="7">
                    <h3>
                        <titulos>NOVEDADES SOBRE LA INSTALACI&Oacute;N Y LOS BIENES DE PROPIEDAD DEL CLIENTE</titulos>
                    </h3>
                </td>
            </tr>
            <tr>
                {% for actaEntrega in cuerpo %}
                {% if actaEntrega.pregunta != "Servicios Contratados" and actaEntrega.flag == "true" %}

            <tr colspan="7" rowspan="1">
                <td colspan="7">
                    <contenido>
                        <b>{% autoescape %}{{ actaEntrega.pregunta|raw }}{% endautoescape %}</b>
                    </contenido>
                </td>
            </tr>
            <tr colspan="7" rowspan="1">
                <td colspan="7">
                    <contenidoTabla>{{ actaEntrega.respuesta }}</contenidoTabla>
                </td>
            </tr>
            {% endif %}
            {% endfor %}

            <tr>
                <td colspan="7" >
                    <contenidoTabla>
                        <p>Esta acta es evidencia de la entrega del medio f&iacute;sico para el servicio
                            contratado.</p>
                    </contenidoTabla>
                </td>
            </tr>
            <tr>
              <td colspan="4">
                        <center>
                            <img height="250px" width="250px" src="{{firmaEmpleado}}" />
                            <figcaption>T&eacute;cnico responsable</figcaption>
                        </center>
                </td>
                <td colspan="4">
                        <center>
                            <img height="250px" width="250px" src="{{firmaCliente}}" />
                            <figcaption>Firma Cliente</figcaption>
                        </center>
                </td>
            </tr>
            <tr>
                <td colspan="100%">
                    <contenidoTabla>
                        <p>Grupo Telconet-Documento confidencial. Prohibida su distribuci&oacute;n sin
                            previa autorizaci&oacute;n</p>
                    </contenidoTabla>
                </td>
            </tr>
        </tbody>
    </table>
</figure>
</html>
'
);

dbms_output.put_line('The length of the manipulated LOB is '||dbms_lob.getlength(plantillaHtml));

UPDATE 
	DB_COMUNICACION.ADMI_PLANTILLA 
SET 
	PLANTILLA = plantillaHtml,
    USR_ULT_MOD = 'wvera'
where CODIGO = 'ACT-ENT-TN-VIS';
commit;
end;
/