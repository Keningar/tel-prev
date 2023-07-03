/**
 * DEBE EJECUTARSE EN DB_GENERAL
 * Se configura parametros de tramas de mensajes para BUSDEPAGOS EXTRANET
 * Se configura usuario para BUSDEPAGOS EXTRANET
 *
 * @author José Bedón Sánchez <jobedon@telconet.ec>
 * @version 1.0 13-03-2020 - Versión Inicial.
 */


INSERT 
INTO DB_GENERAL.ADMI_PARAMETRO_CAB VALUES
  (DB_GENERAL.SEQ_ADMI_PARAMETRO_CAB.NEXTVAL,'BUSPAGOS','Configuracion de Bus de Pagos','FINANCIERO','BUSPAGOS','Activo','jobedon',SYSDATE,
    '127.0.0.1', null, null, null);

INSERT
INTO DB_GENERAL.ADMI_PARAMETRO_DET VALUES
  (DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,(SELECT A.ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB A WHERE A.NOMBRE_PARAMETRO = 'BUSPAGOS'),
    'TRAMA COMENTARIO PARA PAGO LINEA DE EXTRANET','EXTRANET BANCO INTERNACIONAL','{{tipoTransaccion}} - Botón de Pagos con TC {{tipoTc}} - referencia: {{numeroReferencia}}','telcos_bp',null,'Activo','jobedon',SYSDATE,
    '127.0.0.1',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL);

COMMIT;

/