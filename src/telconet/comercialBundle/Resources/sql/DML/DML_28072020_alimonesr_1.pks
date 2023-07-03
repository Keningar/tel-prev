set define off

/**
 * Se crea dml para plantilla para envio de notificaciones por cambio de datos de facturacion.
 * @author Adrian Limones <alimonesr@telconet.ec>
 * @since 1.0 05-08-2020
 */

INSERT INTO
  DB_COMUNICACION.ADMI_PLANTILLA
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
    'CAMBIO DATOS FACTURACION',    
    'CAMBIODATOSFACT',
    'FINANCIERO',
    '<html>
<div>&nbsp;
<table width="100%" cellspacing="0" cellpadding="5" align="center">
<tbody>
<tr>
<td colspan="2" style="border: 1px solid #6699CC; background-color: #e5f2ff;" align="center"><img src="http://images.telconet.net/others/sit/notificaciones/logo.png" alt="" /></td>
</tr>
</tbody>
<tbody>
<tr>
<td colspan="2">Estimado personal,</td>
</tr>
<tr>
<td colspan="2">Se realizan cambios de datos de facturacion:</td>
</tr>
  <tr>
<td colspan="2"><hr><strong></strong></hr></td>
</tr>
<tr>
<td><strong>Razon Social / Nombres y Apellidos:</strong></td>
<td>cliente name apellido</td>
</tr>
<tr>
<td><strong>Ruc Cedula o Pasaporte:</strong></td>
<td>identificacion</td>
</tr>
<tr>
<td><strong>Usuario modificacion:</strong></td>
<td>usuariocreacion</td>
</tr>
<tr>
<td><strong>Fecha modificacion:</strong></td>
<td>fechamodificacion</td>
</tr>
<tr>
<td colspan="2"><hr><strong>Datos Actuales:</strong></hr></td>
</tr>
<tr>
<td><strong>Paga Iva:</strong></td>
<td>pagaivaactual</td>
</tr>
<tr>
<td><strong>Contribucion Solidaria:</strong></td>
<td>contribucionsolidariaactual</td>
</tr>
<tr>
<td><strong>Es Prepago:</strong></td>
<td>tipofacturacionactual</td>
</tr>
<tr>
<td colspan="2"><hr><strong>Datos Nuevos:</strong></hr></td>
</tr>
<tr>
<td><strong>Paga Iva:</strong></td>
<td>pagaivamodificado</td>
</tr>
<tr>
<td><strong>Contribucion Solidaria:</strong></td>
<td>contribucionsolidariamodificado</td>
</tr>
<tr>
<td><strong>Es Prepago:</strong></td>
<td>tipofacturacionmodificado</td>
</tr>
</tbody>
</table>
</div>
</html>

',
    'Activo',
    SYSDATE,
    'alimonesr'
  );
 
INSERT INTO
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
      SELECT ID_ALIAS
      FROM DB_COMUNICACION.ADMI_ALIAS
      WHERE VALOR      = 'facturacion_gye@telconet.ec'
      AND ESTADO       IN ('Activo','Modificado')
      AND EMPRESA_COD  = '10'    
      AND USR_CREACION = 'rsalgado'
    ),
    (
      SELECT ID_PLANTILLA
      FROM DB_COMUNICACION.ADMI_PLANTILLA
      WHERE CODIGO = 'CAMBIODATOSFACT'
      AND ESTADO   = 'Activo'
    ),
    'Activo',
    SYSDATE,
    'alimonesr',
    'NO'
  );

INSERT INTO
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
      SELECT ID_ALIAS
      FROM DB_COMUNICACION.ADMI_ALIAS
      WHERE VALOR      = 'facturacion_uio@telconet.ec'
      AND ESTADO       IN ('Activo','Modificado')
      AND EMPRESA_COD  = '10'    
    ),
    (
      SELECT ID_PLANTILLA
      FROM DB_COMUNICACION.ADMI_PLANTILLA
      WHERE CODIGO = 'CAMBIODATOSFACT'
      AND ESTADO   = 'Activo'
    ),
    'Activo',
    SYSDATE,
    'alimonesr',
    'NO'
  );  

COMMIT;

/
