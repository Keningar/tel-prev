/**
 * DEBE EJECUTARSE EN DB_GENERAL
 * Script para crear cabecera y detalle de parametro de canales para formas de contacto
 * @author Pedro Velez <psvelez@telconet.ec>
 * @version 1.0 09-11-2021 - Versión Inicial.
 */

DECLARE
 Ln_IdParametro number;
 Lv_MensajeError VARCHAR2(500);

BEGIN

Ln_IdParametro := DB_GENERAL.SEQ_ADMI_PARAMETRO_CAB.NEXTVAL;

 insert into DB_GENERAL.ADMI_PARAMETRO_CAB 
 (ID_PARAMETRO,NOMBRE_PARAMETRO,DESCRIPCION,MODULO,ESTADO,USR_CREACION,FE_CREACION,IP_CREACION)
 VALUES
 (Ln_IdParametro,
 'COMERCIAL_GENERICO',
 'PARAMETRO GENERICO PARA MODULO COMERCIAL',
 'SOPORTE',
 'Activo',
 'psvelez',
 sysdate,
 '127.0.0.1');
 
   insert into DB_GENERAL.ADMI_PARAMETRO_DET 
   (id_parametro_det, parametro_id, descripcion, valor1, estado, usr_creacion, fe_creacion, ip_creacion, observacion)
   values 
   (DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL, 
    Ln_IdParametro, 
    'CANALES_WS_FORMA_CONTACTO', 
    'EXTRANET,APPMOVIL',  
    'Activo', 
    'psvelez', 
    SYSDATE, 
    '127.0.0.1',  
    'Parametro para la validacion de canales');
    COMMIT;
EXCEPTION
 WHEN OTHERS THEN
     Lv_MensajeError := SQLCODE || ' -ERROR- ' || SQLERRM ;    
    ROLLBACK;
    DB_GENERAL.GNRLPCK_UTIL.INSERT_ERROR( 'Telcos+',
                                          'CREACION PARAMETRO MD',
                                          Lv_MensajeError,
                                          NVL(SYS_CONTEXT('USERENV','HOST'), 'DB_GENERAL'),
                                          SYSDATE,
                                          NVL(SYS_CONTEXT('USERENV','IP_ADDRESS'),
                                          '127.0.0.1')
                                        );
END;

/