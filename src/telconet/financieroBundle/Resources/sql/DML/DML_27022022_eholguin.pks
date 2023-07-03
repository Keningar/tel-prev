
/**
 * Creación de parámetros para establecer horacios de ejecución de facturación proporcional para Ecuanet, Creación de plantilla de notificación.
 * @version 1.0
 * @author Edgar Holguín <eholguin@telconet.ec>
 * @since 27-FEB-2023
 */

SET DEFINE OFF;
INSERT
INTO DB_GENERAL.ADMI_PARAMETRO_DET DET VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.nextval,
    (SELECT ID_PARAMETRO
    FROM DB_GENERAL.ADMI_PARAMETRO_CAB
    WHERE NOMBRE_PARAMETRO = 'HORARIO DE PROCESAMIENTO PROPORCIONAL' ),
    'HORARIO PROPORCIONAL EN',
    'proporcional',
    'EN',
    '23',
    '40',
    'Activo',
    'eholguin',
    SYSDATE,
    '172.17.0.1',
    'eholguin',
    SYSDATE,
    '172.17.0.1',
    '00',
    (SELECT COD_EMPRESA FROM DB_COMERCIAL.INFO_EMPRESA_GRUPO WHERE NOMBRE_EMPRESA = 'ECUANET'),
    NULL,
    NULL,
    NULL,
    NULL,
    NULL
  );
  
  
INSERT
INTO DB_GENERAL.ADMI_PARAMETRO_DET DET VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.nextval,
    (SELECT ID_PARAMETRO
    FROM DB_GENERAL.ADMI_PARAMETRO_CAB
    WHERE NOMBRE_PARAMETRO = 'HORARIO DE PROCESAMIENTO PROPORCIONAL' ),
    'HORARIO REACTIVACIÓN EN',
    'reactivacion',
    'EN',
    '23',
    '45',
    'Activo',
    'eholguin',
    SYSDATE,
    '172.17.0.1',
    'eholguin',
    SYSDATE,
    '172.17.0.1',
    '00',
    (SELECT COD_EMPRESA FROM DB_COMERCIAL.INFO_EMPRESA_GRUPO WHERE NOMBRE_EMPRESA = 'ECUANET'),
    NULL,
    NULL,
    NULL,
    NULL,
    NULL
  ); 
  
  
INSERT
INTO DB_GENERAL.ADMI_PARAMETRO_DET DET VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.nextval,
    (SELECT ID_PARAMETRO
    FROM DB_GENERAL.ADMI_PARAMETRO_CAB
    WHERE NOMBRE_PARAMETRO = 'HORARIO DE PROCESAMIENTO PROPORCIONAL' ),
    'HORARIO CAMBIO PRECIO EN',
    'cambioPrecio',
    'EN',
    '23',
    '57',
    'Activo',
    'eholguin',
    SYSDATE,
    '172.17.0.1',
    'eholguin',
    SYSDATE,
    '172.17.0.1',
    '00',
    (SELECT COD_EMPRESA FROM DB_COMERCIAL.INFO_EMPRESA_GRUPO WHERE NOMBRE_EMPRESA = 'ECUANET'),
    NULL,
    NULL,
    NULL,
    NULL,
    NULL
  ); 
  
  
INSERT
INTO
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
    'NOTIFICACION_FAC_PROPORCIONEN',    
    'FAC_PROEN',
    'FINANCIERO',
    '<html><head><meta http-equiv=Content-Type content="text/html; charset=UTF-8">
<style type="text/css">
table.cssTable
{ font-family: verdana,arial,sans-serif;font-size:11px;color:#333333;border-width: 1px;border-color: #999999;border-collapse: collapse;width: 70%;}
table.cssTable th {
background-color:#c3dde0;border-width: 1px;padding: 8px;border-style: solid;border-color: #a9c6c9;
}
table.cssTable tr {
background-color:#d4e3e5;}
table.cssTable td {
border-width: 1px;padding: 8px;border-style: solid;border-color: #a9c6c9;
}
table.cssTblPrincipal{
font-family: verdana,arial,sans-serif;font-size:12px;
}</style>
</head>
<body>
      <table class = "cssTblPrincipal" align="center" width="100%" cellspacing="0" cellpadding="5">
        <tr>
          <td align="center" style="border:1px solid #6699CC;background-color:#E5F2FF;">        
            <img alt=""  src="http://images.telconet.net/others/sit/notificaciones/logo.png"/>
          </td>
        </tr>
        <tr>
          <td style="border:1px solid #6699CC;">
	      <table width="100%" cellspacing="0" cellpadding="5">
		<tr><td colspan="2">Estimado personal,</td></tr>      
		<tr><td colspan="2">El presente correo es para informar lo siguiente:</td></tr>
		<tr><td colspan="2">Resumen de Ejecuci&oacute;n de Proceso de Facturaci&oacute;n Proporcional - Tipo: tipoProporcional</td></tr>  
		<tr><td colspan="2">Facturas creadas en estado Pendiente: contadorFacturasPendientes</td></tr>
		<tr><td></td></tr>
                <tr><td><th>Telconet S.A</th></td></tr>
	      </table>
	   </td>
       </tr>
     </table>
</body></html>',
    'Activo',
    SYSDATE,
    'eholguin'
  );
  
COMMIT;
/  
  
    
  
