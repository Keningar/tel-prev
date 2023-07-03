---- INSERT PARA PARAMETRIZAR DISEÑO DE PLANTILLA PARA WS DE FORMA CONTACTO EXTRANET


INSERT INTO DB_GENERAL.ADMI_PARAMETRO_CAB
  (
    ID_PARAMETRO,
    NOMBRE_PARAMETRO,
    DESCRIPCION,
    MODULO,
    ESTADO,
    USR_CREACION,
    FE_CREACION,
    IP_CREACION
  )
  VALUES
  (
     DB_GENERAL.SEQ_ADMI_PARAMETRO_CAB.NEXTVAL,
    'PARAMS_PLANTILLA_EXTRANET_CONT',
    'SE PARAMETRIZA LAS PLANTILLAS PARA CONTACTO EXTRANET',
    'COMERCIAL',
    'Activo',
    'imata',
     SYSDATE,
    '127.0.0.1'
  );

/* DB_GENERAL.ADMI_PARAMETRO_DET */
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
  (
    ID_PARAMETRO_DET,
    PARAMETRO_ID,
    DESCRIPCION,
    VALOR1,
    VALOR2,
    VALOR3,
    VALOR4,
    VALOR5,
    ESTADO,
    USR_CREACION,
    FE_CREACION,
    IP_CREACION,
    EMPRESA_COD
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (
      SELECT ID_PARAMETRO        
      FROM DB_GENERAL.ADMI_PARAMETRO_CAB 
      WHERE NOMBRE_PARAMETRO   = 'PARAMS_PLANTILLA_EXTRANET_CONT' 
      AND ESTADO      = 'Activo'
    ),
    'PLANTILLA_ERROR_EXTRANET_CONTACTO',
    'NOTIF_EXT_CONT',
    'Error al consultar los datos “Contacto persona”del cliente',
    'informaticos@netlife.net.ec',
     NULL,
    'NOTIF_CONT_EXT',
    'Activo',
    'imata',
    SYSDATE,
    '127.0.0.1',
    18
  );
/
SET DEFINE OFF;
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
    'Notificacion de correo de contacto para la Extranet',
    'NOTIF_EXT_CONT',
    'COMERCIAL',
    '<html>
      <head>
        <meta http-equiv=Content-Type content="text/html; charset=UTF-8">
      </head>
      <body>
        <table align="center" width="100%" cellspacing="0" cellpadding="5">
          <tr>
            <td style="border:1px solid #6699CC;">
              <table width="100%" cellspacing="0" cellpadding="5">
                <tr>
                  <td>
                    <strong>Aplicativo:</strong>
                  </td>
                  <td> Extranet </td>
                </tr>
                {% if strTipoCorreo is defined and strTipoCorreo == "CONTACTO" %}
                  <tr>
                    <td>
                      <strong>Canal:</strong>
                    </td>
                    <td> {{strCanal}} </td>
                  </tr>
                {% endif %}
                {% if strTipoCorreo is defined and strTipoCorreo == "CONTACTO" %}
                  <tr>
                    <td>
                      <strong>WS:</strong>
                    </td>
                    <td> {{strWS}} </td>
                  </tr>
                {% endif %}
                {% if strTipoCorreo is defined and strTipoCorreo == "CONTACTO" %}
                  <tr>
                    <td>
                      <strong>Descripcion:</strong>
                    </td>
                    <td> {{strDescripcion}} </td>
                  </tr>
                {% endif %}
                {% if strTipoCorreo is defined and strTipoCorreo == "CONTACTO" %}
                  <tr>
                    <td>
                      <strong>Identificacion del Cliente:</strong>
                    </td>
                    <td> {{strIdentificacion}} </td>
                  </tr>
                {% endif %}
                {% if strOrigen is defined and strOrigen == "PUNTO" %}
                  <tr>
                    <td>
                      <strong>PUNTO:</strong>
                    </td>
                    <td> {{strLogin}} </td>
                  </tr>
                {% endif %}

                {% if strTipoCorreo is defined and (strTipoCorreo == "CONTACTO") %}
                  <tr>
                    <td>
                      <strong>Mensaje Técnico:</strong>
                    </td>
                    <td> {{strError}} </td>
                  </tr>
                {% endif %}
               
              </table>
            </td>
          </tr>
        </table>
      </body>
    </html>',
    'Activo',
    CURRENT_TIMESTAMP,
    'imata'
  );

/


INSERT
INTO
  DB_COMUNICACION.INFO_ALIAS_PLANTILLA
  (
    ID_ALIAS_PLANTILLA,
    ALIAS_ID,
    PLANTILLA_ID,
    ESTADO,
    FE_CREACION,
    USR_CREACION,
    ES_COPIA
  )
  VALUES
  (
    DB_COMUNICACION.SEQ_INFO_ALIAS_PLANTILLA.NEXTVAL,
    (
      SELECT MAX(ID_ALIAS)
      FROM DB_COMUNICACION.ADMI_ALIAS
      WHERE VALOR      = 'informaticos@netlife.net.ec'
      AND ESTADO       IN ('Activo','Modificado')
      AND EMPRESA_COD  = '18'    
    ),
    (
      SELECT ID_PLANTILLA
      FROM DB_COMUNICACION.ADMI_PLANTILLA
      WHERE CODIGO = 'NOTIF_EXT_CONT'
      AND ESTADO   = 'Activo'
    ),
    'Activo',
    SYSDATE,
    'imata',
    'NO'
  );


COMMIT;


/