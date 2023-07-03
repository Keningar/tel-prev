
delete DB_COMERCIAL.ADMI_TIPO_SOLICITUD
where DESCRIPCION_SOLICITUD='SOLICITUD FACTURACION ACUMULADA';

delete DB_COMERCIAL.ADMI_CARACTERISTICA
where DESCRIPCION_CARACTERISTICA='NUM_FACTURA_ACUMULADA' and USR_CREACION='mdleon';

delete DB_COMERCIAL.ADMI_CARACTERISTICA
where DESCRIPCION_CARACTERISTICA='VAL_FACTURA_ACUMULADA' and USR_CREACION='mdleon';

delete DB_COMERCIAL.ADMI_CARACTERISTICA
where DESCRIPCION_CARACTERISTICA='VENDEDOR_FACTURA' and USR_CREACION='mdleon';

delete DB_GENERAL.ADMI_MOTIVO
where NOMBRE_MOTIVO='Facturas no emitidas en meses anteriores por no contar con Orden de Compra.'
and USR_CREACION='mdleon';

delete DB_GENERAL.ADMI_MOTIVO
where NOMBRE_MOTIVO='Facturas no emitidas en meses anteriores por no contar con Orden de Registro.'
and USR_CREACION='mdleon';

delete DB_GENERAL.ADMI_MOTIVO
where NOMBRE_MOTIVO='Facturas no emitidas en meses anteriores por no contar con presupuesto aprobado.'
and USR_CREACION='mdleon';

delete DB_GENERAL.ADMI_MOTIVO
where NOMBRE_MOTIVO='Facturas emitidas anticipadamente por pedido del cliente para pre-pagar meses.'
and USR_CREACION='mdleon';

delete DB_GENERAL.ADMI_MOTIVO
where NOMBRE_MOTIVO='Por error de ingreso.'
and USR_CREACION='mdleon';

delete DB_GENERAL.ADMI_MOTIVO
where NOMBRE_MOTIVO='Por inconveniente en el sistema que no permite poner NRC y lo ponen en MRC.'
and USR_CREACION='mdleon';

delete DB_GENERAL.ADMI_MOTIVO
where NOMBRE_MOTIVO='Traslados que se reflejan como MRC que en realidad son NRC.'
and USR_CREACION='mdleon';

delete DB_GENERAL.ADMI_MOTIVO
where NOMBRE_MOTIVO='El sistema no permite los pagos mensuales, ejemplo consumos (Netvoice o Setel)'
and USR_CREACION='mdleon';

delete DB_GENERAL.ADMI_PARAMETRO_DET
where PARAMETRO_ID=(SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO='CAMBIO_FACTURA_COMISION' AND MODULO='COMERCIAL');

delete DB_GENERAL.ADMI_PARAMETRO_CAB 
where NOMBRE_PARAMETRO='CAMBIO_FACTURA_COMISION' and USR_CREACION='mdleon';

COMMIT;

/