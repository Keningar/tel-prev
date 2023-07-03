/**
 * @author Wilson Quinto <wquinto@telconet.ec>
 * @version 1.0
 * @since 27-09-2021    
 * Se crea la sentencia DML para regularizacion de documentos financieros en produccion
 */
Update DB_FINANCIERO.INFO_DOCUMENTO_FINANCIERO_CAB  idfc set idfc.ESTADO_IMPRESION_FACT='Activo' where idfc.ID_DOCUMENTO in (select ipd.REFERENCIA_ID
from DB_FINANCIERO.info_pago_historial iph
join DB_FINANCIERO.info_pago_det ipd on ipd.PAGO_ID=iph.PAGO_ID 
join DB_FINANCIERO.INFO_DOCUMENTO_FINANCIERO_CAB idf on idf.ID_DOCUMENTO=ipd.REFERENCIA_ID
where iph.USR_CREACION='pmasivo');

INSERT INTO DB_FINANCIERO.INFO_DOCUMENTO_HISTORIAL 
                  (ID_DOCUMENTO_HISTORIAL, DOCUMENTO_ID, FE_CREACION, USR_CREACION, ESTADO, OBSERVACION,MOTIVO_ID)
                  select DB_FINANCIERO.SEQ_INFO_DOCUMENTO_HISTORIAL.nextval,ipd.REFERENCIA_ID,SYSDATE, 'pmasivo','Activo', '[Proceso anulacion pago (regularizacion por referencia id)]',null
from DB_FINANCIERO.info_pago_historial iph
join DB_FINANCIERO.info_pago_det ipd on ipd.PAGO_ID=iph.PAGO_ID 
join DB_FINANCIERO.INFO_DOCUMENTO_FINANCIERO_CAB idf on idf.ID_DOCUMENTO=ipd.REFERENCIA_ID
where iph.USR_CREACION='pmasivo'; 

Update DB_FINANCIERO.info_pago_det ipd set ipd.REFERENCIA_ID=null where ipd.PAGO_ID in (select ipd.PAGO_ID
from DB_FINANCIERO.info_pago_historial iph
join DB_FINANCIERO.info_pago_det ipd on ipd.PAGO_ID=iph.PAGO_ID 
where iph.USR_CREACION='pmasivo');

commit;