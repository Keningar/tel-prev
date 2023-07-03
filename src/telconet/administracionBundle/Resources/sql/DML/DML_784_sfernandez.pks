INSERT
INTO DB_COMUNICACION.ADMI_PLANTILLA
  (
    ID_PLANTILLA,
    NOMBRE_PLANTILLA,
    CODIGO,
    MODULO,
    PLANTILLA,
    ESTADO,
    FE_CREACION,
    USR_CREACION
  )
  VALUES
  (
    DB_COMUNICACION.SEQ_ADMI_PLANTILLA.NEXTVAL,
    'Notificacion de creacion de cuenta de correo electronico',
    'CREA_CTA_CORREO',
    'ADMINISTRACION',
    '<html>
      <head>
        <meta http-equiv=Content-Type content="text/html; charset=UTF-8">
      </head>
      <body>
        <table align="center" width="100%" cellspacing="0" cellpadding="5">
          <tr>
            <td align="center" style="border:1px solid #6699CC;background-color:#E5F2FF;">
                <img alt="" src="http://images.telconet.net/others/sit/notificaciones/logo.png"/>
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
                    La presente notificaci&oacute;n es para indicarle que se cre&oacute; su cuenta en el sistema:
                  </td>
                </tr>
                <tr>
                  <td>
                    <strong>Login:</strong>
                  </td>
                  <td> {{ strLogin }} </td>
                </tr>
                <tr>
                  <td>
                    <strong>Contrase&ntilde;a:</strong>
                  </td>
                  <td> {{ strContrasena }}</td>
                </tr>
              </table>
            </td>
          </tr>
          <tr>
              <td>
              </td>
          </tr>
          <tr>
              <td><strong><font size="2" face="Tahoma">{{ strNombreEmpresa }}</font></strong></p></td>
          </tr>
        </table>
      </body>
    </html>',
    'Activo',
    CURRENT_TIMESTAMP,
    'sfernandez'
  );
        