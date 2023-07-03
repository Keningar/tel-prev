-- QUITANDO PERMISO DE SELECT A TABLA DESDE ESQUEMA DB_GENERAL
REVOKE SELECT ON DB_SOPORTE.ADMI_PROGRESOS_TAREA FROM DB_GENERAL;

-- QUITANDO PERMISO DE SELECT A TABLA DESDE ESQUEMA DB_GENERAL
REVOKE SELECT ON DB_SOPORTE.ADMI_TIPO_PROGRESO FROM DB_GENERAL;

--ELIMINANDO SECUENCIA	SEQ_ADMI_PROGRESOS_TAREA
DROP sequence DB_SOPORTE.SEQ_ADMI_PROGRESOS_TAREA;

--ELIMINANDO TABLA DB_SOPORTE.ADMI_PROGRESOS_TAREA
DROP TABLE DB_SOPORTE.ADMI_PROGRESOS_TAREA;
			
--ELIMINANDO PROGRESOS DE TAREA DE PRODUCTO WIFI+AP
DELETE FROM 
DB_SOPORTE.INFO_PROGRESO_PORCENTAJE 
WHERE 
TAREA_ID = -3
EMPRESA_ID = 18 AND 
USR_CREACION = 'rmoranc';	

--ELIMINANDO PROGRESOS DE TAREA DE PRODUCTO CABLEADO
DELETE FROM 
DB_SOPORTE.INFO_PROGRESO_PORCENTAJE 
WHERE 
TAREA_ID = -4
EMPRESA_ID = 18 AND 
USR_CREACION = 'rmoranc';	

--ELIMINAMOS DETALLE DE ACTIVACION_PRODUCTOS_MEGADATOS
DELETE FROM DB_GENERAL.ADMI_PARAMETRO_DET DETALLE
WHERE
    PARAMETRO_ID = (
        SELECT
            ID_PARAMETRO
        FROM
            DB_GENERAL.ADMI_PARAMETRO_CAB
        WHERE
            NOMBRE_PARAMETRO = 'ACTIVACION_PRODUCTOS_MEGADATOS'
    );
    
--ELIMINAMOS CABECERA DE ACTIVACION_PRODUCTOS_MEGADATOS
DELETE FROM DB_GENERAL.ADMI_PARAMETRO_CAB CABECERA
WHERE
    CABECERA.NOMBRE_PARAMETRO = 'ACTIVACION_PRODUCTOS_MEGADATOS';
	
--ELIMINANDO PLANTILLA DE PRODUCTO CABLEADO MD 	
DELETE FROM 
DB_COMUNICACION.ADMI_PLANTILLA
WHERE CODIGO = 'ACT-MD-INS-CABL' AND USR_CREACION = 'rmoranc';
	
--Eliminando ID de producto cableado Md
Delete from DB_GENERAL.ADMI_PARAMETRO_DET
where  VALOR1 = 'ID_PRODUCTO_CABLEADO_MD';

--Eliminando ID de producto WIFI+AP Md
Delete from DB_GENERAL.ADMI_PARAMETRO_DET
where  VALOR1 = 'ID_PRODUCTO_WIFI+AP';
	
	
	
--ACTUALIZANDO PLANTILLA DE ACTA DE ENTREGA DE SERVICIO MEGADATOS.

SET DEFINE OFF 
UPDATE DB_COMUNICACION.ADMI_PLANTILLA 
SET USR_ULT_MOD = 'nnaulal',
    FE_ULT_MOD = TO_CHAR(SYSDATE,'YYYY-MM-DD HH24:MI:SS'),
    PLANTILLA =
TO_CLOB('
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
            background: #f9e314;
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
            background: #f69e18;
        }
        #netlife
        {
            font-size:9px;  
        }
        label, .labelGris
        { 
            background: #E6E6E6;
        }
    </style>
	') || TO_CLOB('
  </head>
    <body> 
        <div id="bienvenido">ACTA DE ENTREGA DEL SERVICIO ó VISITA TECNICA </div>
        <div align="center" style="float: right;">
            <img src="/home/telcos/web/public/images/logo_netlife_big.jpg" alt="log" title="NETLIFE"  height="50">
            <table id="netlife">
                <tr>  
                    <td align="center">1-700 NETLIFE (638-543)</td>
                </tr>
                <tr>  
                    <td align="center">ó al 37-31-300</td>
                </tr>
            </table>
        </div>  
        <div style="clear: both;"></div>

        <div style="font-size:11px; position: absolute; margin-top:-20px;" ><b>Fecha (aaaa-mm-dd): </b><label>{{ fecha }}</label></div>
') || TO_CLOB('
        <p>DATOS DEL CLIENTE</p>
        <table width="100%" border="0" class="box-section-content" style="font-size:11px;">
            <tr>
                {% if datosCliente[''NOMBRES'']!="" %}
                   <td style="width: 15%"><b>Nombre del Cliente:</b></td>
                   <td style="width: 50%" class ="labelGris">{{ datosCliente[''NOMBRES''] }}</td>
                   <td style="width: 15%"><b>CI:</b></td>
                   <td style="width: 30%" class ="labelGris">{{ datosCliente[''IDENTIFICACION_CLIENTE''] }}</td>
                {% else %}
                   <td style="width: 15%"><b>Razon Social:</b></td>
                   <td style="width: 50%" class ="labelGris">{{ datosCliente[''RAZON_SOCIAL''] }}</td>
                   <td style="width: 15%"><b>RUC:</b></td>
                   <td style="width: 30%" class ="labelGris">{{ datosCliente[''IDENTIFICACION_CLIENTE''] }}</td>
                {% endif %}
            </tr>
            <tr>
                <td style="width: 15%"><b>Login:</b></td>
                <td style="width: 50%" class ="labelGris">{{ datosCliente[''LOGIN''] }}</td>
                <td style="width: 15%"><b>Coordenadas:</b></td>
                <td style="width: 30%" class ="labelGris">{{ datosCliente[''LONGITUD''] }},{{ datosCliente[''LATITUD''] }}</td>
            </tr>
            <tr>
                <td style="width: 15%"><b>Dirección:</b></td>
                <td style="width: 50%" class ="labelGris">{{ datosCliente[''DIRECCION''] }}</td>
            </tr>
        </table>
        <!-- --------------------------------------------------------------------------------------------------------------- -->
') || TO_CLOB('
       <p>DATOS DE CONTACTO</p>
        <table width="100%" border="0" class="box-section-content" style="font-size:11px">
            <tr>
                <td style="width: 15%;"><b>Persona de Contacto:</b></td>
                <td style="width: 50%" class ="labelGris">
                    {% if contactoCliente %}
                        {{ contactoCliente[''NOMBRE_CONTACTO''] }}
                    {% else %}
                        NA
                    {% endif %}
                </td>
            </tr>
            {% if formaContactoPunto[''total''] > 1 %}
               {% for contacto in formaContactoPunto[''registros''] %}
                   <tr>
                       <td style="width: 15%"><b>{{ contacto[''descripcionFormaContacto''] }}:</b></td>
                       <td style="width: 50%" class ="labelGris">{{ contacto[''valor''] }}</td>
                   </tr>
               {% endfor %}
            {% elseif formaContactoCliente[''total''] > 0 %}
               {% for contacto in formaContactoCliente[''registros''] %}
                   <tr>
                       <td style="width: 15%"><b>{{ contacto[''descripcionFormaContacto''] }}:</b></td>
                       <td style="width: 50%" class ="labelGris">{{ contacto[''valor''] }}</td>
                   </tr>
               {% endfor %}
            {% endif %}
        </table>
        <!-- --------------------------------------------------------------------------------------------------------------- -->
') || TO_CLOB('
      <p>SERVICIOS CONTRATADOS</p>
        <table width="100%" border="0" class="box-section-content" style="font-size:11px">
            <tr>
                <td style="width: 15%"><b>Nombre del Plan:</b></td>
                <td style="width: 50%" class ="labelGris">{{ servicio.planId.nombrePlan }}</td>
                <td style="width: 15%"><b>Tipo Orden:</b></td>
                <td style="width: 30%" class ="labelGris">{{ servicio.tipoOrden }}</td>
            </tr>
            <tr>
                <td style="width: 15%"><b>Ultima Milla:</b></td>
                <td style="width: 50%" class ="labelGris">{{ ultimaMilla.nombreTipoMedio }}</td>
                <td style="width: 15%"><b>Comparticion:</b></td>
                <td style="width: 30%" class ="labelGris">{{ comparticion }}</td>
            </tr>
        </table>
') || TO_CLOB('
       {% set idPregunta = 0 %}
       {% set observacion = "" %}
       {% for actaEntrega in cuerpo %}
           {% if actaEntrega.pregunta == "Observaciones" %} 
              {% set observacion = actaEntrega.respuesta %}    
           {% elseif idPregunta == actaEntrega.idPregunta%}
               <tr>
                   {% if actaEntrega.flag == "true" %}
                       <td style="width: 2%; text-align: center;" class ="labelGris">&#10004;</td>
                   {% else %}
                       <td style="width: 2%; text-align: center;" class ="labelGris">&#10008;</td>
                   {% endif %}
                   <td  style="width: 10%" class ="labelGris">{{ actaEntrega.respuesta }}</td>                    
               </tr>
           {% else %}
               {% if idPregunta != 0 %}
                   </table>
               {% endif %}

               {% set idPregunta = actaEntrega.idPregunta %}
               <table align="center" width="20%" class="box-section-content" 
                      style="float: left; font-size:9px; border: 1px solid black; alignment-adjust: central; padding: 1%; margin: 1% 2% 1% 2%;">
               <tr>
                   <th id="pregunta" colspan="2" style="border: 1px solid black;">{{ actaEntrega.pregunta }}</th>
               </tr>
               <tr>
                   {% if actaEntrega.flag == "true" %}
                       <td style="width: 2%; text-align: center;" class ="labelGris">&#10004;</td>
                   {% else %}
                       <td style="width: 2%; text-align: center;" class ="labelGris">&#10008;</td>
                   {% endif %}
                   <td style="width: 10%" class ="labelGris">{{ actaEntrega.respuesta }}</td>
               </tr>
           {% endif %}
       {% endfor %}
       </table>
       <div style="clear: both;"></div>
       <span style="font-size:10px"><b>Nota:</b> &#10008; Significa que no fue marcado.</span>
        <!-- --------------------------------------------------------------------------------------------------------------- -->
') || TO_CLOB('
		<p>EQUIPOS ENTREGADOS</p>
        <table id="equipos" width="100%" class="box-section-content" style="font-size:11px; text-align: center; border-collapse: collapse;">
             <th>Tipo</th>
            <th>Modelo</th>
            <th>Marca</th>
            <th>Serie</th>
            <th>Mac</th>
           {% for elemento in arrayElementosRegis %}
              <tr>
                  <td style="width:20%" class ="labelGrisCelda">{{ elemento[''tipo''] }}</td>
                  <td style="width:20%" class ="labelGrisCelda">{{ elemento[''modelo''] }}</td>
                  <td style="width:20%" class ="labelGrisCelda">{{ elemento[''marca''] }}</td>
                  <td style="width:20%" class ="labelGrisCelda">{{ elemento[''serie''] }}</td>
                  <td style="width:20%" class ="labelGris">
                     {% if elemento[''mac''] is not null %}
                         {{ elemento[''mac''] }}
                     {% else %}
                        NA
                     {% endif %}
                  </td>
              </tr>
           {% endfor %}
        </table>
') || TO_CLOB('
        <!-- --------------------------------------------------------------------------------------------------------------- -->
        {% if materiales|length > 0 %}

          <p>MATERIAL EXCEDENTE UTILIZADO</p>
          <table id="equipos" width="100%" class="box-section-content" style="font-size:11px; text-align: center; border-collapse: collapse;">
              <tr>
                <th rowspan="2">Material</th>
                <th colspan="3">Cantidad</th>
                <th rowspan="2">($) Valor Unitario</th>
                <th rowspan="2">($) Valor Adicional</th>
              </tr>
              <tr>
                <th>Usada</th>
                <th>Incluida</th>
                <th>Excedente</th>
              </tr>

              {% for material in materiales %}
                <tr>
                  <td style="width:40%; text-align: left;" class ="labelGris" >{{ material.materialDescripcion }}</td>
                  <td style="width:12%" class ="labelGris">{{ material.cantidadUsada }}</td>
                  <td style="width:12%" class ="labelGris">{{ material.cantidadEmpresa }}</td>
                  <td style="width:12%" class ="labelGris">{{ material.cantidadFacturada }}</td>
                  <td style="width:12%" class ="labelGris">{{ material.costoMaterial }}</td>
                  <td style="width:12%" class ="labelGris">{{ material.materialCostoExcedente }}</td>
                </tr>
              {% endfor %}
          </table>
          <table id="totalMateriales" width="100%" style="font-size:11px; text-align: center; border-collapse: collapse; border:0;">
              <tr>
                  <td style="width:88%; text-align: right; padding-right:5px; font-weight: bold;" colspan="4" >TOTAL</td>
                  <td style="width:12%; border:1px solid black;" class ="labelGris">{{totalMateriales}}</td>
              </tr>
          </table>
          <div style="text-align: justify; text-justify: inter-word; font-size:10px; padding:5px;" >El cliente, titular del contrato, declara que conoce y acepta que existen materiales adicionales utilizados para la instalación, que exceden los definidos en el contrato, dentro de los cuales se encuentra un máximo de 250mts de fibra óptica entre el splitter y el ONT instalado donde el cliente. Adicionalmente acepta los valores adicionales que se describen en esta acta.</div>

        {% endif %}
') || TO_CLOB('
        <!-- --------------------------------------------------------------------------------------------------------------- -->
        {% if observacion is defined %}
            <p>OBSERVACIÓN</p>
            <div style="font-size:9px;">{{ observacion | raw }}</div>
        {% endif %}
        <!-- --------------------------------------------------------------------------------------------------------------- -->

       <p>TERMINOS Y CONDICIONES</p>
        <div style="font-size:9px;">{{ terminosCondiciones | raw }}</div>

        <!-- --------------------------------------------------------------------------------------------------------------- -->

        <table style="font-size:11px; text-align: center; border-collapse: collapse;">
            <tr>
                <td style="width: 50%"><img src="{{firmaEmpleado}}" alt="firma" width="250"/></td>
                <td style="width: 50%"><img src="{{firmaCliente}}" alt="firma" width="250"/></td>
            </tr>
            <tr>
                <td style="width: 50%"><span><hr style="width: 70%;"/>Firma Netlife</span></td>
                <td style="width: 50%"><span><hr style="width: 70%;"/>Firma Cliente</span></td>
            </tr>
        </table>
') || TO_CLOB('
        <!-- --------------------------------------------------------------------------------------------------------------- -->

        <div style=" width: 15%; font-size: 9px; float: right;">
            <span>FO-INS-01 | FO-SN2-01</span>
            <br>
            <span>ver-08 | Sep-2015</span>
        </div>
        <div style="clear: both;"></div>
         <!-- --------------------------------------------------------------------------------------------------------------- -->     
    </body>
</html> 
')
WHERE CODIGO = 'ACT-ENT-MD-INS';


	

	
COMMIT;

