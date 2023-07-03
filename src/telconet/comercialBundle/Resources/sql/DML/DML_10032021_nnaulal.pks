/* PARAMETROS */
/* CREACIÓN DEL PARÁMETRO CAB  - REPROGRAMAR_DEPARTAMENTO_HAL*/
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
    'PARAMS_PLANTILLA_EXTRANET',
    'SE PARAMETRIZA LAS PLANTILLAS PARA EXTRANET',
    'COMERCIAL',
    'Activo',
    'nnaulal',
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
      WHERE NOMBRE_PARAMETRO   = 'PARAMS_PLANTILLA_EXTRANET' 
      AND ESTADO      = 'Activo'
    ),
    'PLANTILLA_ERROR_EXTRANET',
    'NOTIF_EXTRANET',
    'ASUNTO DEL EXTRANET',
    'notificacionesnetlife@netlife.info.ec',
    'calidad@netlife.net.ec',
    'NOTIF_CLIE_EXT',
    'Activo',
    'nnaulal',
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
    'Notificacion de correo de notificiacion de Extranet',
    'NOTIF_EXTRANET',
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
                {% if strTipoCorreo is defined and strTipoCorreo != "CATALOGO" %}
                  <tr>
                    <td>
                      <strong>Login Origen:</strong>
                    </td>
                    <td> {{strLoginOrigen}} </td>
                  </tr>
                {% endif %}
                {% if strTipoCorreo is defined and strTipoCorreo == "TRASLADO" %}
                  <tr>
                    <td>
                      <strong>Login Destino:</strong>
                    </td>
                    <td> {{strLoginDestino}} </td>
                  </tr>
                {% endif %}
                {% if strTipoCorreo is defined and strTipoCorreo != "CATALOGO" %}
                  <tr>
                    <td>
                      <strong>Identificación del cliente:</strong>
                    </td>
                    <td> {{strIdentificacion}} </td>
                  </tr>
                {% endif %}
                {% if strTipoCorreo is defined and (strTipoCorreo == "PUNTO" or strTipoCorreo == "CATALOGO" or strTipoCorreo == "TRASLADO" ) %}
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
    'nnaulal'
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
    'Notificacion de correo de notificiacion de Extranet',
    'NOTIF_CLIE_EXT',
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
                    <strong> Estimado cliente:</strong>
                  </td>
                </tr>
                <tr>
                  <td>
                    {% if strTipoCorreo is defined and strTipoCorreo == "TRASLADO_MANUAL" %}
                      <strong>Su solicitud de traslado está en proceso.</strong>
                    {% else %}
                       Se ha registrado correctamente el traslado del punto de origen {{strLoginOrigen}}
                       al punto de destino {{strLoginDestino}}.
                    {% endif %}
                  </td>
                </tr>
                {% if strTipoCorreo is defined and strTipoCorreo == "TRASLADO_MANUAL" %}
                  <tr>
                    <td>
                      <strong>Aplicativo:</strong>
                    </td>
                    <td> Extranet </td>
                  </tr>
                  <tr>
                    <td>
                      <strong>Login Origen:</strong>
                    </td>
                    <td> {{strLoginOrigen}} </td>
                  </tr>
                  <tr>
                    <td>
                      <strong>Login Destino:</strong>
                    </td>
                    <td> {{strLoginDestino}} </td>
                  </tr>
                  <tr>
                    <td>
                      <strong>Identificación del cliente:</strong>
                    </td>
                    <td> {{strIdentificacion}} </td>
                  </tr>
                  <tr>
                    <td>
                      <strong>Estado del servicio:</strong>
                    </td>
                    <td> {{strEstadoServicio}} </td>
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
    'nnaulal'
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
      WHERE CODIGO = 'NOTIF_EXTRANET'
      AND ESTADO   = 'Activo'
    ),
    'Activo',
    SYSDATE,
    'nnaulal',
    'NO'
  );

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
      WHERE VALOR      = 'soporte@netlife.net.ec'
      AND ESTADO       IN ('Activo','Modificado')
      AND EMPRESA_COD  = '18'    
    ),
    (
      SELECT ID_PLANTILLA
      FROM DB_COMUNICACION.ADMI_PLANTILLA
      WHERE CODIGO = 'NOTIF_EXTRANET'
      AND ESTADO   = 'Activo'
    ),
    'Activo',
    SYSDATE,
    'nnaulal',
    'NO'
  );

-----------------------------

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
      WHERE VALOR      = 'soporte@netlife.net.ec'
      AND ESTADO       IN ('Activo','Modificado')
      AND EMPRESA_COD  = '18'    
    ),
    (
      SELECT ID_PLANTILLA
      FROM DB_COMUNICACION.ADMI_PLANTILLA
      WHERE CODIGO = 'NOTIF_CLIE_EXT'
      AND ESTADO   = 'Activo'
    ),
    'Activo',
    SYSDATE,
    'nnaulal',
    'NO'
  );
        
COMMIT;
/
