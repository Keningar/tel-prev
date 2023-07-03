/**
 * @author  Wilson Quinto <wquito@telconet.ec>
 * @version 1.0 Plantillas para notificación de pagos anulados.
 * @since   05-05-2021 
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
    'Notifica de anulaciÃ³n pago',
    'NOTI_AP_OK',
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
                <tr><td colspan="2">Estimado personal,</td></tr>
                <tr>
                <td colspan="2">
                El presente correo es para indicarle el estado de los pagos anuladas.
                </td>
                </tr>
                <tr><td colspan="2">Alguna novedad favor notificar a Sistemas</td></tr>
                  <tr><td colspan="2"><hr /></td></tr>
                </table></td>
                </tr>
                <tr><td></td></tr></table>
          </body>
    </html>',
    'Activo',
    SYSDATE,
    'wquinto',
    NULL,
    NULL,
    '18'
    );
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
    'Notifica de anulación pago por error',
    'NOTI_AP_DOC',
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
                <tr><td colspan="2">Estimado personal,</td></tr>
                <tr>
                <td colspan="2">
                El presente correo es para indicarle los documento asociados a los pagos anulados.
                </td>
                </tr>
                <tr><td colspan="2">Alguna novedad favor notificar a Sistemas</td></tr>
                  <tr><td colspan="2"><hr /></td></tr>
                </table></td>
                </tr>
                <tr><td></td></tr></table>
          </body>
    </html>',
    'Activo',
    SYSDATE,
    'wquinto',
    NULL,
    NULL,
    '18'
    );
COMMIT;
/
