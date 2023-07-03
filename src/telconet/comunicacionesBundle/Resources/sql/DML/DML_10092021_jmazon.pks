/**
 *
 * Se crean plantilla para el formulario de soporte L2 del producto ECDF
 *	 
 * @author Jonathan Mazon <jmazon@telconet.ec>
 * @version 1.0 10-09-2021
 */


SET DEFINE OFF
--PLANTILLAS CORREO
INSERT INTO DB_COMUNICACION.ADMI_PLANTILLA
(ID_PLANTILLA, NOMBRE_PLANTILLA, CODIGO, MODULO, PLANTILLA, ESTADO, FE_CREACION, USR_CREACION, FE_ULT_MOD, USR_ULT_MOD, EMPRESA_COD)
VALUES
 ( DB_COMUNICACION.SEQ_ADMI_PLANTILLA.NEXTVAL, 'Formulario Soporte L2 El canal del futbol', 'ECDF_L2', 'TECNICO',
TO_CLOB('<html>

<head>
    <meta http-equiv=Content-Type content="text/html; charset=UTF-8">
</head>

<body>
    <table align="center" width="100%" cellspacing="0" cellpadding="5">
        <tr>
            <td align="center" style="border:1px solid #6699CC;background-color:#E5F2FF;">
                <img alt="" src="http://images.telconet.net/others/telcos/logo.png" />
            </td>
        </tr>') || TO_CLOB('
        <tr>
            <td style="border:1px solid #6699CC;">
                <table width="100%" cellspacing="0" cellpadding="5">
                    <tr>
                        <td colspan="2">Estimado personal,</td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            El presente correo es para informarle que se cre&oacute; un formulario de reporte en el cual
                            se detallan posibles daños en la plataforma {{ nombreProducto }} de acuerdo con lo indicado
                            por el cliente.
                        </td>
                    </tr>') || TO_CLOB('
                    <tr>
                        <td colspan="2">
                            <hr />
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" style="text-align: center;">
                            <strong>Informaci&oacute;n del Ticket</strong>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <hr />
                        </td>
                    </tr>') || TO_CLOB('
                    <tr>
                        <td>
                            <strong>Ticket/ Identificador de Netlife:</strong>
                        </td>
                        <td>{{ ticket }}</td>
                    </tr>
                    <tr>
                        <td>
                            <strong>Nombre Cliente:</strong>
                        </td>
                        <td>
                            {{ nombreCompleto }}
                        </td>
                    </tr>') || TO_CLOB('
                    <tr>
                        <td>
                            <strong>Correo Electrónico:</strong>
                        </td>
                        <td>
                            {{ correoElectronico }}
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <hr />
                        </td>
                    </tr>') || TO_CLOB('
                    <tr>
                        <td colspan="2" style="text-align: center;">
                            <strong>Resumen del Problema</strong>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <hr />
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <strong>Usuario del Servicio:</strong>
                        </td>
                        <td>
                            <p>{{ usuario }}</p>
                        </td>
                    </tr>') || TO_CLOB('
                    
                    <tr>
                        <td>
                            <strong>Dispositivo:</strong>
                        </td>
                    </tr>

                  <tr style="text-align: center;border:1px solid #6699CC;">
                      <td>
                        <strong>Tipo:</strong>
                      </td>
                      <td>
                          <p>{{ tipo }}</p>
                      </td>
                 </tr>') || TO_CLOB('
                  <tr  style="text-align: center;">
                      <td>
                          <strong>Marca:</strong>
                      </td>
                      <td>
                        <p>{{ marca }}</p>
                      </td>
                  </tr>
                  <tr style="text-align: center;">
                  	<td>
                      <strong>Modelo:</strong>
                    </td>
                    <td>
                    	<p>{{ modelo }}</p>
                    </td>
                  </tr>') || TO_CLOB('
                  <tr>
                  	<td></td>
                  </tr>
                  <tr>
                      <td>
                          <strong>Descripci&oacute;n Problema:</strong>
                      </td>
                      <td>
                          <p>{{ descripcionProblema }}</p>
                      </td>
                  </tr>
                  <td colspan="2"><br /></td>

                </table>
            </td>
        </tr>') || TO_CLOB('
        <tr>
            <td><strong>
                    <font size="2" face="Tahoma">MegaDatos S.A.</font>
                </strong></p>
            </td>
        </tr>
    </table>
</body>

</html>'), 'Activo', SYSDATE, 'jmazon', NULL, NULL, NULL);




COMMIT;

/