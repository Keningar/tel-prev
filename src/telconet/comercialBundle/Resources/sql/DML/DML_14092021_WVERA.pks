--Actualizando Plantillas acta de soporte TN	
SET SERVEROUTPUT ON 200000;
declare
    plantillaHtml
 clob:='<!DOCTYPE html>';
begin

DBMS_LOB.APPEND(plantillaHtml, 
'
<html>
  <head>
    <meta http-equiv=Content-Type content="text/html; charset=UTF-8">
    <style>
        body
        {
            border: 1px solid black;
        }
        p
        {
            font-weight: bold;
            background: #5cb2e5;
            font-size:12px; 
            border-top: 1px black solid; 
            border-bottom: 1px solid black;
        }
        x
        {
            font-weight: bold;
            font-size:12px; 
            border-top: 1px black solid; 
            border-bottom: 1px solid black;
        }
        #bienvenido
        {
            font-weight: bold;
            font-size:18px; 
            position: absolute;
        }
        #pregunta
        {
            background: #f69e18;
        }
        #equipos, #equipos th
        {
            border: 1px solid black;
        }
        #equipos th
        {
            background: #00579a;
            color: white;
            padding: 5;
        }
        #netlife
        {
            font-size:9px;  
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
    </style>
  </head>
    <body>
        <table width="100%" style="font-size:11px; border-collapse: collapse; text-align: center;">
            <th style="width: 25%; border: 1px solid black;"><img src={{ imagenCabecera }} alt="log" title="TELCONET"  height="50"></th>
            <th colspan="3" style="border: 1px solid black;">ACTA DE RETIRO DE EQUIPOS PARA SERVICIOS CANCELADOS</th>
            <th style="width: 20%; border: 1px solid black;">FOR OPU 07 <br>Versión: 2 (02 08 2021)</th>
        </table>      
        <p>DATOS GENERALES</p>
        <table width="100%" border="0" class="box-section-content" style="font-size:11px;">
            <tr>
                {% if datosCliente['RAZON_SOCIAL'] is empty %}
                   <td style="width: 15%"><b>Nombre del Cliente:</b></td>
                   <td style="width: 50%" class ="labelGris">{{ datosCliente['NOMBRES'] }}</td>
                   <td style="width: 15%"><b>CI/Razon Social/RUC:</b></td>
                   <td style="width: 30%" class ="labelGris">{{ datosCliente['IDENTIFICACION_CLIENTE'] }}</td>
                {% else %}
                   <td style="width: 15%"><b>Razón Social:</b></td>
                   <td style="width: 50%" class ="labelGris">{{ datosCliente['RAZON_SOCIAL'] }}</td>
                   <td style="width: 15%"><b>CI/Razon Social/RUC:</b></td>
                   <td style="width: 30%" class ="labelGris">{{ datosCliente['IDENTIFICACION_CLIENTE'] }}</td>
                {% endif %}
            </tr>
            <tr>
                <td style="width: 15%"><b>Login:</b></td>
                <td style="width: 50%" class ="labelGris">{{ datosCliente['LOGIN'] }}</td>
                <td style="width: 15%"><b>Coordenadas:</b></td>
                <td style="width: 30%" class ="labelGris">{{ datosCliente['LONGITUD'] }},{{ datosCliente['LATITUD'] }}</td>
            </tr>
            <tr>
                <td style="width: 15%"><b>Dirección:</b></td>
                <td style="width: 50%" class ="labelGris">{{ datosCliente['DIRECCION'] }}</td>
                <td style="width: 15%"><b>Fecha Retiro Equipo (aaaa-mm-dd):</b></td>
                <td style="width: 30%" class ="labelGris">{{ fecha }}</td>
            </tr>
        </table>
        <!-- --------------------------------------------------------------------------------------------------------------- -->

        <p>DATOS DE CONTACTO</p>
        <table width="100%" border="0" class="box-section-content" style="font-size:11px">
            <tr>
                <td style="width: 15%;"><b>Persona de Contacto:</b></td>
                <td style="width: 50%" class ="labelGris">
                    {% if contactoCliente %}
                        {{ contactoCliente['NOMBRE_CONTACTO'] }}
                    {% else %}
                        NA
                    {% endif %}
                </td>
            </tr>
        </table>
        <!-- --------------------------------------------------------------------------------------------------------------- -->

        <p>SERVICIOS CONTRATADOS</p>
        <table width="100%" border="0" class="box-section-content" style="font-size:11px">
            <tr>
                <td style="width: 15%"><b>Nombre Plan:</b></td>
                <td style="width: 50%" class ="labelGris">{{ servicio.productoId.descripcionProducto }}</td>
                <td style="width: 15%"><b>Tipo Orden:</b></td>
                <td style="width: 30%" class ="labelGris">{{ servicio.tipoOrden }}</td>
            </tr>
            <tr>
                <td style="width: 15%"><b>Última Milla:</b></td>
                <td style="width: 50%" class ="labelGris">{{ ultimaMilla.nombreTipoMedio }}</td>
            </tr>
        </table>

        <p>EQUIPOS ENTREGADOS</p>
        <table id="equipos" width="100%" class="box-section-content" style="font-size:11px; text-align: center; border-collapse: collapse;">
            <th>Tipo Elemento</th>
            <th>Cargador?</th>
            <th>Marca</th>
            <th>Serie</th>
            <th>Estado del Equipo</th>
            {% if elementosRetiro['total'] > 0 %}
               {% for elemento in elementosRetiro['elementos'] %}
                  <tr>
                      <td style="width:20%" class ="labelGrisCelda">{{ elemento['tipoElemento'] }}</td>
                      <td style="width:20%" class ="labelGrisCelda">{{ elemento['cargador'] }}</td>
                      <td style="width:20%" class ="labelGrisCelda">{{ elemento['marcaElemento'] }}</td>
                      <td style="width:20%" class ="labelGrisCelda">{{ elemento['serieElemento'] }}</td>
                      <td style="width:20%" class ="labelGrisCelda">{{ elemento['estadoElemento'] }}</td>
                  </tr>
               {% endfor %}
            {% endif %}
        </table>
        <!-- --------------------------------------------------------------------------------------------------------------- -->
        <p>OBSERVACIONES</p>
        <div style="font-size:9px;">{{ observaciones | raw }}</div>
        
        <table style="width: 100%; font-size:11px; text-align: left; border-collapse: collapse;">
            <th style="width: 33%; border: 1px solid black; text-align: center;">Técnico que retira:</th>
            <th style="width: 33%; border: 1px solid black; text-align: center;">Cliente:</th>
            <th style="width: 33%; border: 1px solid black; text-align: center;">Recibido en Bodega:</th>
            <tr>
                <td style="width: 33%; border: 1px solid black;">Firma: <img src="{{firmaEmpleado}}" alt="firma" width="75%"/></td>
                <td style="width: 33%; border: 1px solid black;">Firma: <img src="{{firmaCliente}}" alt="firma" width="75%"/></td>
                <td style="width: 33%; border: 1px solid black;"></td>
            </tr>
            <tr>
                {% if firmaEmpresa == true %}
                  <td style="width: 33%; border: 1px solid black;">Nombre: Ing. Hugo Proaño</td>
                {% else %}
                  <td style="width: 33%; border: 1px solid black;">Nombre: {{ nombreEmpleado }}</td>
                {% endif %}
                <td style="width: 33%; border: 1px solid black;">Nombre: {{ nombreCliente }}</td>
                <td style="width: 33%; border: 1px solid black;">Nombre:</td>
            </tr>
            <tr>
                <td style="width: 33%; border: 1px solid black;">Cédula: {{ cedulaEmpleado }}</td>
                <td style="width: 33%; border: 1px solid black;">Cédula: {{ cedulaCliente }}</td>
                <td style="width: 33%; border: 1px solid black;">Cédula:</td>
            </tr>
        </table>

    </body>
</html>
'
);

dbms_output.put_line('The length of the manipulated LOB is '||dbms_lob.getlength(plantillaHtml));

UPDATE 
	DB_COMUNICACION.ADMI_PLANTILLA 
SET 
	PLANTILLA = plantillaHtml,
    USR_ULT_MOD = 'wvera'
where CODIGO = 'ACT-RET-TN';
commit;
end;
/