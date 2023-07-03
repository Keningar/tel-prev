/**
 * @author Madeline Haz <mhaz@telconet.ec>
 * @version 1.0
 * @since 12-04-2019 
 *
 * @author Edgar Holguín <eholguin@telconet.ec>
 * @version 1.1 Se realizan cambios para la versión final.
 *   
 * Se crea plantillas para notificación de reporte diario por cambios de forma de pago.
 */
-- Inserta nueva plantilla para notificar creación de reporte.
SET DEFINE OFF;
INSERT INTO DB_COMUNICACION.ADMI_PLANTILLA(
  ID_PLANTILLA,
  NOMBRE_PLANTILLA,
  CODIGO,
  MODULO,
  PLANTILLA,
  ESTADO,
  FE_CREACION,
  USR_CREACION,
  FE_ULT_MOD,
  USR_ULT_MOD,
  EMPRESA_COD
) VALUES (
    DB_COMUNICACION.SEQ_ADMI_PLANTILLA.NEXTVAL,
    'Notifica Creación de Rpt Cambio Forma Pago',
    'CAMB_FORMPAG',
    'FINANCIERO',
    '<html>
        <head>
        <meta http-equiv=Content-Type content="text/html; charset=UTF-8">
        <style type="text/css">table.cssTable { font-family: verdana,arial,sans-serif;font-size:11px;color:#333333;border-width: 1px;border-color: #999999;border-collapse: collapse;}table.cssTable th {background-color:#c3dde0;border-width: 1px;padding: 8px;border-style: solid;border-color: #a9c6c9;}table.cssTable tr {background-color:#d4e3e5;}table.cssTable td {border-width: 1px;padding: 8px;border-style: solid;border-color: #a9c6c9;}table.cssTblPrincipal{font-family: verdana,arial,sans-serif;font-size:12px;}</style>
        </head>
          <body>
            <table class = "cssTblPrincipal" align="center" width="100%" cellspacing="0" cellpadding="5">
            <tr>
            <td align="center" style="border:1px solid #6699CC;background-color:#E5F2FF;">
            <img alt=""  src="http://images.telconet.net/others/sit/notificaciones/logo.png"/>
            </td>
            </tr>
            <tr><td style="border:1px solid #6699CC;">
                <table width="100%" cellspacing="0" cellpadding="5">
                <tr><td colspan="2">Estimados,</td></tr>
                <tr>
                <td colspan="2">
                En el presente mail se adjunta Reporte de Cambios de Forma de Pago.
                Alguna novedad favor notificar a Sistemas.
                </td></tr>
                <tr><td colspan="2">
                    <table class = "cssTable"  align="center" >
                        <tr><th> Login</th>
                            <th> Forma de pago anterior </th>
                            <th> Forma de pago actual </th>
                            <th> N&uacute;mero de Acta </th>
                            <th> Fecha de Activaci&oacute;n </th>
                            <th> Usuario </th>
                            <th> Motivo </th>
                            <th> Genera Factura </th>
                            <th> No.Factura </th>
                            <th> Fecha Emisi&oacute;n</th>
                            <th> Valor </th>
                       </tr>
                           {{ plContratoHist | raw }}
                    </table>
                  </td></tr>
                  <tr><td colspan="2"><hr /></td></tr>
                </table></td>
                </tr>
                <tr><td></td></tr></table>
          </body>
    </html>',
    'Activo',
    SYSDATE,
    'mhaz',
    NULL,
    NULL,
    '18'
    );

-- Insertar Alias Para Notificación De Reporte.
INSERT INTO DB_COMUNICACION.INFO_ALIAS_PLANTILLA (
  ID_ALIAS_PLANTILLA,
  ALIAS_ID,
  PLANTILLA_ID,
  ESTADO,
  FE_CREACION,
  USR_CREACION,
  ES_COPIA
) VALUES (
    DB_COMUNICACION.SEQ_INFO_ALIAS_PLANTILLA.NEXTVAL,
    ( SELECT ID_ALIAS
      FROM   DB_COMUNICACION.ADMI_ALIAS
      WHERE  VALOR       = 'ncolta@netlife.net.ec'
      AND    ESTADO IN     ('Activo','Modificado')
      AND    EMPRESA_COD = 10    
    ),
    ( SELECT ID_PLANTILLA
      FROM   DB_COMUNICACION.ADMI_PLANTILLA
      WHERE  CODIGO = 'CAMB_FORMPAG'
      AND    ESTADO = 'Activo'
    ),
    'Activo',
    SYSDATE,
    'mhaz',
    'NO'
  );

INSERT INTO DB_COMUNICACION.INFO_ALIAS_PLANTILLA(
  ID_ALIAS_PLANTILLA,
  ALIAS_ID,
  PLANTILLA_ID,
  ESTADO,
  FE_CREACION,
  USR_CREACION,
  ES_COPIA
) VALUES (
    DB_COMUNICACION.SEQ_INFO_ALIAS_PLANTILLA.NEXTVAL,
    ( SELECT ID_ALIAS
      FROM   DB_COMUNICACION.ADMI_ALIAS
      WHERE  VALOR       = 'dbravo@netlife.net.ec'
      AND    ESTADO IN     ('Activo','Modificado')
      AND    EMPRESA_COD = 18    
    ),
    ( SELECT ID_PLANTILLA
      FROM   DB_COMUNICACION.ADMI_PLANTILLA
      WHERE  CODIGO = 'CAMB_FORMPAG'
      AND    ESTADO = 'Activo'
    ),
    'Activo',
    SYSDATE,
    'mhaz',
    'NO'
  );
INSERT INTO DB_COMUNICACION.INFO_ALIAS_PLANTILLA(
  ID_ALIAS_PLANTILLA,
  ALIAS_ID,
  PLANTILLA_ID,
  ESTADO,
  FE_CREACION,
  USR_CREACION,
  ES_COPIA
) VALUES (
    DB_COMUNICACION.SEQ_INFO_ALIAS_PLANTILLA.NEXTVAL,
    ( SELECT ID_ALIAS
      FROM   DB_COMUNICACION.ADMI_ALIAS
      WHERE  VALOR       = 'ssalazar@netlife.net.ec'
      AND    ESTADO IN     ('Activo','Modificado')
      AND    EMPRESA_COD = 18    
    ),
    ( SELECT ID_PLANTILLA
      FROM   DB_COMUNICACION.ADMI_PLANTILLA
      WHERE  CODIGO = 'CAMB_FORMPAG'
      AND    ESTADO = 'Activo'
    ),
    'Activo',
    SYSDATE,
    'mhaz',
    'NO'
  );

-- Inserta nueva plantilla para notificar creación de reporte.
INSERT INTO DB_COMUNICACION.ADMI_PLANTILLA(
  ID_PLANTILLA,
  NOMBRE_PLANTILLA,
  CODIGO,
  MODULO,
  PLANTILLA,
  ESTADO,
  FE_CREACION,
  USR_CREACION,
  FE_ULT_MOD,
  USR_ULT_MOD,
  EMPRESA_COD
) VALUES (
    DB_COMUNICACION.SEQ_ADMI_PLANTILLA.NEXTVAL,
    'Notificación de Cambio de Forma Pago',
    'CFP_CLT',
    'FINANCIERO',
    '<html>
        <head>
        <meta http-equiv=Content-Type content="text/html; charset=UTF-8">
        <style type="text/css">table.cssTable { font-family: verdana,arial,sans-serif;font-size:11px;color:#333333;border-width: 1px;border-color: #999999;border-collapse: collapse;}table.cssTable th {background-color:#c3dde0;border-width: 1px;padding: 8px;border-style: solid;border-color: #a9c6c9;}table.cssTable tr {background-color:#d4e3e5;}table.cssTable td {border-width: 1px;padding: 8px;border-style: solid;border-color: #a9c6c9;}table.cssTblPrincipal{font-family: verdana,arial,sans-serif;font-size:12px;}</style>
        </head>
          <body>
            <table class = "cssTblPrincipal" align="center" width="100%" cellspacing="0" cellpadding="5">
            <tr>
            <td align="center" style="border:1px solid #6699CC;background-color:#E5F2FF;">
            <img alt=""  src="http://images.telconet.net/others/sit/notificaciones/logo.png"/>
            </td>
            </tr>
            <tr><td style="border:1px solid #6699CC;">
                <table width="100%" cellspacing="0" cellpadding="5">
                <tr><td colspan="2">Estimados,</td></tr>
                <tr>
                  <td colspan="2">
                    strMessage .
                  </td>
                </tr>
                <tr><td colspan="2"><hr /></td></tr>
                </table></td>
                </tr>
                <tr><td></td></tr></table>
          </body>
    </html>',
    'Activo',
    SYSDATE,
    'eholguin',
    NULL,
    NULL,
    '18'
    );
COMMIT;
/
