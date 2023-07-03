/**
 * @author  Edgar Holguín <eholguin@telconet.ec>
 * @version 1.0 Plantillas para notificación de reporte de puntos a considerar en facturación por instalacion.
 * @since   11-06-2020 
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
    'RPT_FACTINST',
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
                El presente correo es para indicarle un reporte general de puntos a facturar.
                </td>
                </tr>
                <tr><td colspan="2">Alguna novedad favor notificar a Sistemas</td></tr>
                <tr><td colspan="2">
                    <table class = "cssTable"  align="center" >
                        <tr>
                            <th> Cliente </th>
                            <th> Login</th>
                            <th> Servicio </th>                            
                            <th> Pto.Facturaci&oacute;n </th>
                            <th> Estado Servicio </th>
                            <th> Tipo Orden</th>
                            <th> Paga Iva</th>
                            <th> Co&oacute;digo Tipo Negocio </th>
                            <th> Precio Venta </th>
                            <th> Precio Instalaci&oacute;n </th>
                            <th> Genera Factura </th>
                       </tr>
                           {{ plPtosFacturar | raw }}
                    </table>
                  </td></tr>
                  <tr><td colspan="2"><hr /></td></tr>
                </table></td>
                </tr>
                <tr>
                  <td colspan="2">
                  <table class = "cssTable"  align="center" > 
                    <tr><th>Tomar en consideraci&oacute;n los siguientes motivos para su revisi&oacute;n:</th></tr>
                    <tr><td colspan="2"> - Precio de instalaci&oacute;n y de venta debe ser mayor a cero.</td></tr>
                    <tr><td colspan="2"> - El producto debe tener Fecuencia diferente de 0.</td></tr>
                    <tr><td colspan="2"> - Pto. de facturaci&oacute;n del servicio debe estar habilitado como padre de facturaci&oacute;n.</td></tr>
                    <tr><td colspan="2"> - C&oacute;digo tipo negocio diferente a ISP.</td></tr> 
                    <tr><td colspan="2"> - Servicio debe estar en estado Activo.</td></tr> 
                  </table>
                    </td>
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
      WHERE  VALOR       = 'facturacion_gye@telconet.ec'
      AND    ESTADO IN     ('Activo','Modificado')
      AND    EMPRESA_COD = 10    
    ),
    ( SELECT ID_PLANTILLA
      FROM   DB_COMUNICACION.ADMI_PLANTILLA
      WHERE  CODIGO = 'RPT_FACTINST'
      AND    ESTADO = 'Activo'
    ),
    'Activo',
    SYSDATE,
    'eholguin',
    'NO'
  );

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
      WHERE  VALOR       = 'facturacion_uio@telconet.ec'
      AND    ESTADO IN     ('Activo','Modificado')
      AND    EMPRESA_COD = 10    
    ),
    ( SELECT ID_PLANTILLA
      FROM   DB_COMUNICACION.ADMI_PLANTILLA
      WHERE  CODIGO = 'RPT_FACTINST'
      AND    ESTADO = 'Activo'
    ),
    'Activo',
    SYSDATE,
    'eholguin',
    'NO'
  );
COMMIT;
/
