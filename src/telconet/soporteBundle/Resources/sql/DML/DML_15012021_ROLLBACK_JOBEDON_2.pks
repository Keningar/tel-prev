DECLARE
  Lv_plantilla1 CLOB := '<html>
  <head>
    <meta http-equiv=Content-Type content="text/html; charset=UTF-8">


  <style type="text/css">
                    table.cssTable { 
                      font-family: verdana,arial,sans-serif;
                      font-size:11px;color:#333333;border-width: 1px;border-color: #999999;border-collapse: collapse;
                    }table.cssTable th {
                      background-color:#c3dde0;border-width: 1px;padding: 8px;border-style: solid;border-color: #a9c6c9;
                    }table.cssTable tr {
                      background-color:#d4e3e5;
                    }table.cssTable td {
                      border-width: 1px;padding: 8px;border-style: solid;border-color: #a9c6c9;
                    }table.cssTblPrincipal{
                     font-family: verdana,arial,sans-serif;font-size:12px;
                    }
                    </style>

  </head>
  <body>
    <table align="center" width="100%" cellspacing="0" cellpadding="5">
      <tr>
        <td align="center" style="border:1px solid #6699CC;background-color:#E5F2FF;">
            <img alt=""  src="http://images.telconet.net/others/sit/notificaciones/logo.png"/>
            {#<img alt=""  src="https://telcos.telconet.ec/public/images/logo.png"/>#}
        </td>
      </tr>
      <tr>
        <td style="border:1px solid #6699CC;">
          <table width="100%" cellspacing="0" cellpadding="5">
            <tr>
                <td colspan="2">Estimado personal,</td>
            </tr>
            <tr>
              <td colspan="2">
                El presente correo es para indicarle que se CERRO el siguiente CASO: 
              </td>
            </tr>
            <tr>
              <td colspan="2">
                <hr />
              </td>
            </tr>
            <tr>
                <td colspan="2" style="text-align: center;">
                <strong>Caso # {{ caso.numeroCaso}}</strong>
              </td>
            </tr>
            <tr>
              <td colspan="2">
                <hr />
              </td>
            </tr>
           
         <tr>
              <td>
                <strong>Afectación:</strong>
              </td>
           </tr>
           
           <tr>
		<td colspan="2">
                                      <table class = "cssTable"  align="center" >
                                          <tr>                  
                                              <th> Tipo Afectacion </th>
                                              <th> Nombre Afectado <br></th>
                                              <th> Descripcion Afectado <br></th>                                                             
                                         </tr>
                                          {% for afectado in afectadoPadre  %}
                                           <tr>                  
                                              <td> {{ afectado.tipoAfectado}} </td>
                                              <td> {{ afectado.afectadoNombre}} </td>
                                              <td> {{ afectado.afectadoDescripcion}} </td>                                                             
					  </tr>
                                          {% endfor %} 
                                      </table>
                               </td>
           </tr>    
           
            {% if tieneDetalle  %}
           
            <tr>
              <td>
                <strong>Detalle Afectación:</strong>
              </td>
           </tr>
           
            <tr>
	      <td colspan="2">
		    <table class = "cssTable"  align="center" >
			<tr>                  
			    <th> Afectacion </th>
			    <th> Nombre <br></th>
			    <th> Descripcion <br></th>                                                             
			</tr>
			{% for afectado in afectadoDetalle %}
			  <tr>                  
			    <td> {{ afectado.tipoAfectado}} </td>
			    <td> {{ afectado.afectadoNombre}} </td>
			    <td> {{ afectado.afectadoDescripcion}} </td>                                                             
			</tr>
			{% endfor %} 
		    </table>
	      </td>
           </tr>
           
           {% endif %}

            <tr>
              <td>
                <strong>Fecha de creacion:</strong>
              </td>
              <td>{{caso.feApertura | date("d-M-Y") }} | {{ caso.feApertura | date("H:i")  }}</td>
            </tr>
            <tr>
              <td>
                <strong>Fecha de cierre:</strong>
              </td>
              <td>{{caso.feCierre | date("d-M-Y") }} | {{ caso.feCierre | date("H:i")  }}</td>
            </tr>
			{% if(asignacion !="")%}
            <tr>
              <td>
                <strong>Asignado:</strong>
              </td>
              <td>{{asignacion.asignadoNombre }}{% if(asignacion.refAsignadoNombre!="")%}-{{asignacion.refAsignadoNombre}}{%endif%}</td>
            </tr>
			{%endif%}
            <tr>
              <td>
                <strong>Titulo Inicial:</strong>
              </td>
              <td>{{ caso.tituloIni }}</td>
            </tr>
            <tr>
              <td>
                <strong>Titulo Final:</strong>
              </td>
              <td>{{ tituloFin }}</td>
            </tr>
            <tr>
              <td>
                <strong>Version Inicial:</strong>
              </td>
              <td>{{ caso.versionIni }}</td>
            </tr>
            <tr>
              <td>
                <strong>Version Final:</strong>
              </td>
              <td>{{ caso.versionFin }}</td>
            </tr>
            <tr>
              <td>
                <strong>Tiempo total:</strong><br />                
              </td>
              <td>
		  {{totalMinutos}} minutos                  
              </td>
            </tr> 
            <tr>
              <td colspan="2"><br/></td>
            </tr>
          </table>
        </td>
      </tr>
      <tr>
          <td>&nbsp;
          </td>
      </tr>
      <tr>
          <td>Por favor revisar el caso <a href="https://telcos.telconet.ec/soporte/info_caso/{{caso.id}}/show">{{caso.numeroCaso}}</a>
          </td>   
      </tr>
      <tr>
          {% if empresa == "TTCO" %}
          <td><strong><font size="2" face="Tahoma">TransTelco S.A.</font></strong></p>
          {% elseif empresa == "MD" %}
          <td><strong><font size="2" face="Tahoma">MegaDatos S.A.</font></strong></p>
          {% else %}
          <td><strong><font size="2" face="Tahoma">Telconet S.A.</font></strong></p>
          {% endif %}
          </td>   
      </tr>  
    </table>
  </body>
</html>';

BEGIN

  UPDATE DB_COMUNICACION.ADMI_PLANTILLA
  SET PLANTILLA = Lv_plantilla1
  WHERE CODIGO  = 'CASOCERRAR';
  
  COMMIT;
  
END;

/