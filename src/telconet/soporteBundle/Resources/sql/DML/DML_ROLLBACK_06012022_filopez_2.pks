/**
 * DEBE EJECUTARSE EN DB_COMUNICACION
 * Script para rollback de añadir contacto de quito en la notificación de pendientes de Telefónica
 * @author Fernando López <filopez@telconet.ec>
 * @version 1.0 06-01-2022 - Versión Inicial.
 */

UPDATE DB_COMUNICACION.ADMI_PLANTILLA SET PLANTILLA = CONCAT('<html> 
  <head> 
    <meta http-equiv=Content-Type content="text/html; charset=UTF-8">
	<style>
	.textMail {
    	  border:1px solid #6699CC;
	  text-align:left;
	  color:#222222;
	  font-family: Arial, Helvetica, sans-serif;
	}
	.textTituloMail {
	  border:1px solid #6699CC;
	  background:#1e81b0;
	  color:#ffffff;
	  font-family: Arial, Helvetica, sans-serif;
	}
	.textMailFirma {
	  text-align:left;
	  color:#222222;
	  font-family: Arial, Helvetica, sans-serif;
	}
	</style>
  </head> 
  <body> 
    <table align="center" width="90%" cellspacing="0" cellpadding="0">
      <tr>
      	<td style="border:0px solid #6699CC;" >
          <table width="100%" cellspacing="0" cellpadding="11"> 
	      <tr>
		<td  class="textTituloMail" colspan="4" style="text-align: center;" >
		TICKET # {{ numeroTicket }}
		</td>
	      </tr>
            <tr> 
                <td colspan="" class="textMail" width="35%"><strong>Cliente:</strong> {{cliente}}</td><td colspan="2" class="textMail"><strong>Reportado por:</strong> {{reportadoPor}}</td>  
            </tr> 
            <tr>
                <td colspan="" class="textMail"><strong>Login:</strong> {{login}}</td><td colspan="2" class="textMail"><strong>Numero de Contacto:</strong> {{numeroContacto}}</td>  
            </tr>
            <tr> 
                <td colspan="" class="textMail"><strong>Fecha y Hora de Inicio:</strong></td><td colspan="" class="textMail">{{fechaInicio}}</td><td colspan="" class="textMail">{{horaInicio}}</td>
            </tr>
            <tr><td colspan="" class="textMail"><strong>Fecha y Hora de Fin:</strong></td><td colspan="" class="textMail">{{fechaFin}}</td><td colspan="" class="textMail">{{horaFin}}</td>
            </tr>
            <tr> 
                <td colspan="" class="textMail"><strong>Tiempo total de Afectaci',
        CONCAT(CHR(38),
        CONCAT('oacute;n:</strong></td>
                <td colspan="2" class="textMail">{{tiempoTotalAfectacion}}</td>
            </tr>
            <tr> 
                <td colspan="" class="textMail"><strong>Circuitos Afectados:</strong></td>
                <td colspan="2" class="textMail">{{circuito}}</td>
            </tr>
            <tr> 
                <td colspan="" class="textMail"><strong>Tramos Afectados:</strong></td>
                <td colspan="2" class="textMail">{{tramo}}</td>
            </tr>
            <tr> 
                <td colspan="" class="textMail"><strong>Evento Reportado:</strong></td>
                <td colspan="2" class="textMail">{{eventoReportado}}</td>
            </tr>
            <tr> 
                <td colspan="" class="textMail"><strong>Acciones Realizadas:</strong></td>
                <td colspan="2" class="textMail">{{accionesRealizadas}}</td>
            </tr>
            <tr> 
                <td colspan="" class="textMail"><strong>Departamento Asignado:</strong></td>
                <td colspan="2" class="textMail">{{departamentoAsignado}}</td>
            </tr>
            <tr> 
                <td colspan="" class="textMail"><strong>Responsable:</strong></td>
                <td colspan="2" class="textMail">{{responsableAsignado}}</td>
            </tr>
          </table> 
        </td> 
      </tr> 
      <tr> 
      <td>',
        CONCAT(CHR(38),CONCAT('nbsp; </td> 
      </tr> 
      <tr> 
          <td>
            <p class="textMailFirma"><strong><font size="3" face="Arial"> Atentamente,</font></strong></br>
            <strong><font size="3" face="Arial">Network Operation Center</font></strong></br>
            <strong><font size="3" face="Arial">Telconet S.A.</font></strong></br>' ,
             '<strong><font size="3" face="Arial" color="#6c94d4"> noc@telconet.ec;</font></strong></br> 
             <strong><font size="3" face="Arial" color="#22044a"> Phone:</font> </strong>
             <strong><font size="2" face="Arial" color="#6c94d4"> (593-4)2680555,</font> </strong>
            <strong><font size="3" face="Arial" color="#22044a"> ext. 5000</font></strong></p> 
          </td>
      </tr>   
    </table> 
  </body> 
</html>')))))
WHERE CODIGO = 'NOTIF_TELFNC'
AND NOMBRE_PLANTILLA = 'NOTIFICACION INICIAL O FINAL CASO TELEFONICA'
AND MODULO = 'SOPORTE';
--
--
COMMIT;
/