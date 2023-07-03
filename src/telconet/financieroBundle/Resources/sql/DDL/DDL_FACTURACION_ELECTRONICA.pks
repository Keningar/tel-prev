--DB_COMPROBANTES
ALTER TRIGGER DB_COMPROBANTES.TRG_AFTER_INFO_DOCUMENTO DISABLE;

ALTER TABLE DB_COMPROBANTES.INFO_DOCUMENTO MODIFY NUMERO_AUTORIZACION VARCHAR2(50);

ALTER TABLE DB_COMPROBANTES.INFO_DOCUMENTO ADD ORIGEN_DOCUMENTO VARCHAR2(30);
COMMENT ON COLUMN DB_COMPROBANTES.INFO_DOCUMENTO.ORIGEN_DOCUMENTO IS 'Campo que identifica el origen del documento - (Online/Offline)';

ALTER TABLE DB_COMPROBANTES.INFO_DOCUMENTO ADD ENVIA_MAIL VARCHAR2(1);
COMMENT ON COLUMN DB_COMPROBANTES.INFO_DOCUMENTO.ENVIA_MAIL IS 'Campo que identifica si el mail fue enviado al cliente S (SI) N (NO)';

ALTER TABLE DB_COMPROBANTES.INFO_DOCUMENTO ADD DOCUMENTO_ID_FINAN NUMBER DEFAULT 0;
COMMENT ON COLUMN DB_COMPROBANTES.INFO_DOCUMENTO.DOCUMENTO_ID_FINAN IS 'Id del documento del la tabla INFO_DOCUMENTO_FINANCIERO_CAB del esquema financiero';

ALTER TRIGGER DB_COMPROBANTES.TRG_AFTER_INFO_DOCUMENTO ENABLE;

/* DML Actualizacion del origen del documento */
update DB_COMPROBANTES.INFO_DOCUMENTO set ORIGEN_DOCUMENTO = 'Online';

/* DML Insert en la tabla ADMI_ESTADO_DOCUMENTO */
INSERT
INTO DB_COMPROBANTES.ADMI_ESTADO_DOCUMENTO
  (
    ID_EST_DOC,
    NOMBRE,
    DESCRIPCION,
    FE_CREACION,
    USR_CREACION,
    IP_CREACION
  )
  VALUES
  (
    '11',
    'EN PROCESO',
    'El documento fue recibido y se encuentra en procesamiento.',
    SYSDATE,
    'admin',
    '127.0.0.1'
  ) ;


/* DML Actualizacion de la plantilla en la tabla INFO_NOTIF_PLANTILLA  */
SET DEFINE OFF 
UPDATE "DB_COMPROBANTES"."INFO_NOTIF_PLANTILLA" SET PLANTILLA = '<table id="ride_datos_comp" style="width:100%; height:100%; border: 1px solid black; border-radius:5px;">
<tr>
<td style="font-weight:bold; font-size: $fontsizepx; padding:5px;">R.U.C.:</td>
<td style="font-size: $fontsizepx;">$empresa_ruc</td>
</tr>
<tr>
<td colspan="2" style="font-weight:bold; font-size:16px; padding:5px;">$comprobante_tipo</td>
</tr>
<tr>
<td style="font-weight:bold; font-size: $fontsizepx; padding:5px;">No.</td>
<td style="font-size: $fontsizepx;">$comprobante_numero</td>
</tr>
<tr>
<td colspan="2" style="font-weight:bold; font-size: $fontsizepx; padding:5px;">N&Uacute;MERO DE AUTORIZACI&Oacute;N</td>
</tr>
<tr>
<td colspan="2" style="font-size: 12px; padding:5px; text-align:center;">$comprobante_numero_autorizacion</td>
</tr>
<tr>
<td style="font-weight:bold; font-size: $fontsizepx; padding:5px;">
<div style="$mostrarFechaHoraAutorizacion">FECHA Y HORA DE<br/>AUTORIZACI&Oacute;N</div>
$espacioFechaHoraAutorizacion
</td>
<td style="font-size: $fontsizepx;">$comprobante_fecha_autorizacion</td>
</tr>
<tr>
<td style="font-weight:bold; font-size: $fontsizepx; padding:5px;">AMBIENTE:</td>
<td style="font-size: $fontsizepx;">$comprobante_ambiente</td>
</tr>
<tr>
<td style="font-weight:bold; font-size: $fontsizepx; padding:5px;">EMISI&Oacute;N:</td>
<td style="font-size: $fontsizepx;">$comprobante_emision</td>
</tr>
<tr>
<td colspan="2" style="font-weight:bold; font-size: $fontsizepx; padding:5px;">CLAVE DE ACCESO</td>
</tr>
<tr>
<td colspan="2" style="text-align:center; font-size: $fontsizepx; padding:5px;">
<img src="http://facturacion.telconet.net/img/clave/$comprobante_clave_acceso.png" />
$comprobante_clave_acceso
</td>
</tr>
</table>' WHERE ID_NOTIF_PLANTILLA = 3;

COMMIT;
