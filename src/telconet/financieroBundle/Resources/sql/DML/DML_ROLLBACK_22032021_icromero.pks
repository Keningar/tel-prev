/**
 * DEBE EJECUTARSE EN DB_FINANCIERO
 * Script eliminar debito detalle para (formato machala) tarjeta mastercard, estos se cargan a elegir una opcion del comboBox Formatos de la pantalla Generar Debitos opcion Visa/Mastercard
 * @author Ivan Romero <icromero@telconet.ec>
 * @version 1.0 22-03-2021 - Version Inicial.
 */
delete  
from DB_FINANCIERO.ADMI_GRUPO_ARCHIVO_DEBITO_DET 
where GRUPO_DEBITO_ID = (SELECT ID_GRUPO_DEBITO FROM DB_FINANCIERO.ADMI_GRUPO_ARCHIVO_DEBITO_CAB WHERE EMPRESA_COD = 18 AND NOMBRE_GRUPO ='Tarjetas Mastercard (Debito Especial Machala)') AND USR_CREACION = 'icromero';
/**
 * DEBE EJECUTARSE EN DB_FINANCIERO
 * Script eliminar debito detalle para (formato machala) tarjeta visa, estos se cargan a elegir una opcion del comboBox Formatos de la pantalla Generar Debitos opcion Visa/Mastercard
 * @author Ivan Romero <icromero@telconet.ec>
 * @version 1.0 22-03-2021 - Version Inicial.
 */
delete  
from DB_FINANCIERO.ADMI_GRUPO_ARCHIVO_DEBITO_DET 
where GRUPO_DEBITO_ID = (SELECT ID_GRUPO_DEBITO FROM DB_FINANCIERO.ADMI_GRUPO_ARCHIVO_DEBITO_CAB WHERE EMPRESA_COD = 18 AND NOMBRE_GRUPO ='Tarjetas Visa (Debito Especial Machala)') AND USR_CREACION = 'icromero';
/**
 * DEBE EJECUTARSE EN DB_GENERAL
 * Script eliminar nuevos parametros y regresar a valor originales los parametros existentes de los formatos(parametros) que toman para la funcionalidad con los comboBox de la pantalla Generar Debitos opcion Visa/Mastercard
 * @author Ivan Romero <icromero@telconet.ec>
 * @version 1.0 22-03-2021 - Version Inicial.
 */
--se actualiza valor3 de parametro de formatos
-- tarjeta visa formato visa se habia puesto inactivo por solicitud del usuario
update  db_general.admi_parametro_det  set estado='Activo',USR_ULT_MOD = null,FE_ULT_MOD = null
where parametro_id = (select id_parametro from db_general.admi_parametro_Cab where nombre_parametro='FORMATOS_DEBITOS' and estado='Activo') and valor1=(SELECT ABTC.ID_BANCO_TIPO_CUENTA
    FROM DB_FINANCIERO.ADMI_BANCO_TIPO_CUENTA ABTC,DB_FINANCIERO.ADMI_BANCO AB,DB_FINANCIERO.ADMI_TIPO_CUENTA ATC
    WHERE ABTC.BANCO_ID        = AB.ID_BANCO
    AND ABTC.TIPO_CUENTA_ID    = ATC.ID_TIPO_CUENTA
    AND ATC.DESCRIPCION_CUENTA = 'TARJETA VISA'
    AND ATC.ESTADO             = 'Activo'
    AND AB.DESCRIPCION_BANCO   = 'TARJETAS'
    AND AB.ESTADO              = 'Activo-debitos');
--tarjeta visa formato austro
update  db_general.admi_parametro_det  set valor3=null,USR_ULT_MOD = null,FE_ULT_MOD = null
where parametro_id = (select id_parametro from db_general.admi_parametro_Cab where nombre_parametro='FORMATOS_DEBITOS' and estado='Activo') and valor1=(SELECT ABTC.ID_BANCO_TIPO_CUENTA
    FROM DB_FINANCIERO.ADMI_BANCO_TIPO_CUENTA ABTC,DB_FINANCIERO.ADMI_BANCO AB,DB_FINANCIERO.ADMI_TIPO_CUENTA ATC
    WHERE ABTC.BANCO_ID        = AB.ID_BANCO
    AND ABTC.TIPO_CUENTA_ID    = ATC.ID_TIPO_CUENTA
    AND ATC.DESCRIPCION_CUENTA = 'TARJETA VISA'
    AND ATC.ESTADO             = 'Activo'
    AND AB.DESCRIPCION_BANCO   = 'BANCO DEL AUSTRO'
    AND AB.ESTADO              = 'Activo');
--tarjeta visa formato machala
delete from db_general.admi_parametro_det  
where parametro_id = (select id_parametro from db_general.admi_parametro_Cab where nombre_parametro='FORMATOS_DEBITOS' and estado='Activo') and valor1=(SELECT ABTC.ID_BANCO_TIPO_CUENTA
    FROM DB_FINANCIERO.ADMI_BANCO_TIPO_CUENTA ABTC,DB_FINANCIERO.ADMI_BANCO AB,DB_FINANCIERO.ADMI_TIPO_CUENTA ATC
    WHERE ABTC.BANCO_ID        = AB.ID_BANCO
    AND ABTC.TIPO_CUENTA_ID    = ATC.ID_TIPO_CUENTA
    AND ATC.DESCRIPCION_CUENTA = 'TARJETA VISA'
    AND ATC.ESTADO             = 'Activo'
    AND AB.DESCRIPCION_BANCO   = 'BANCO MACHALA'
    AND AB.ESTADO              = 'Activo');
--tarjeta mastercard formato mastercard   se habia puesto se inactivo por solicitud del usuario
update  db_general.admi_parametro_det  set estado='Activo',USR_ULT_MOD = null,FE_ULT_MOD = null
where parametro_id = (select id_parametro from db_general.admi_parametro_Cab where nombre_parametro='FORMATOS_DEBITOS' and estado='Activo') and valor1=(SELECT ABTC.ID_BANCO_TIPO_CUENTA
    FROM DB_FINANCIERO.ADMI_BANCO_TIPO_CUENTA ABTC,DB_FINANCIERO.ADMI_BANCO AB,DB_FINANCIERO.ADMI_TIPO_CUENTA ATC
    WHERE ABTC.BANCO_ID        = AB.ID_BANCO
    AND ABTC.TIPO_CUENTA_ID    = ATC.ID_TIPO_CUENTA
    AND ATC.DESCRIPCION_CUENTA = 'TARJETA MASTERCARD'
    AND ATC.ESTADO             = 'Activo'
    AND AB.DESCRIPCION_BANCO   = 'TARJETAS'
    AND AB.ESTADO              = 'Activo-debitos');
--tarjeta mastercard formato austro
update  db_general.admi_parametro_det  set valor3=null,USR_ULT_MOD = null,FE_ULT_MOD = null
where parametro_id = (select id_parametro from db_general.admi_parametro_Cab where nombre_parametro='FORMATOS_DEBITOS' and estado='Activo') and valor1=(SELECT ABTC.ID_BANCO_TIPO_CUENTA
    FROM DB_FINANCIERO.ADMI_BANCO_TIPO_CUENTA ABTC,DB_FINANCIERO.ADMI_BANCO AB,DB_FINANCIERO.ADMI_TIPO_CUENTA ATC
    WHERE ABTC.BANCO_ID        = AB.ID_BANCO
    AND ABTC.TIPO_CUENTA_ID    = ATC.ID_TIPO_CUENTA
    AND ATC.DESCRIPCION_CUENTA = 'TARJETA MASTERCARD'
    AND ATC.ESTADO             = 'Activo'
    AND AB.DESCRIPCION_BANCO   = 'BANCO DEL AUSTRO'
    AND AB.ESTADO              = 'Activo');
--tarjeta mastercard formato machala
delete from  db_general.admi_parametro_det  
where parametro_id = (select id_parametro from db_general.admi_parametro_Cab where nombre_parametro='FORMATOS_DEBITOS' and estado='Activo') and valor1=(SELECT ABTC.ID_BANCO_TIPO_CUENTA
    FROM DB_FINANCIERO.ADMI_BANCO_TIPO_CUENTA ABTC,DB_FINANCIERO.ADMI_BANCO AB,DB_FINANCIERO.ADMI_TIPO_CUENTA ATC
    WHERE ABTC.BANCO_ID        = AB.ID_BANCO
    AND ABTC.TIPO_CUENTA_ID    = ATC.ID_TIPO_CUENTA
    AND ATC.DESCRIPCION_CUENTA = 'TARJETA MASTERCARD'
    AND ATC.ESTADO             = 'Activo'
    AND AB.DESCRIPCION_BANCO   = 'BANCO MACHALA'
    AND AB.ESTADO              = 'Activo');


/**
 * DEBE EJECUTARSE EN DB_FINANCIERO
 * Script se elimina formato de respuesta para tarjetas visa banco machala 
 * @author Ivan Romero <icromero@telconet.ec>
 * @version 1.0 22-03-2021 - Version Inicial.
 */
Delete from DB_FINANCIERO.admi_formato_debito_respuesta where BANCO_TIPO_CUENTA_ID=(SELECT ABTC.ID_BANCO_TIPO_CUENTA
    FROM DB_FINANCIERO.ADMI_BANCO_TIPO_CUENTA ABTC,DB_FINANCIERO.ADMI_BANCO AB,DB_FINANCIERO.ADMI_TIPO_CUENTA ATC
    WHERE ABTC.BANCO_ID        = AB.ID_BANCO
    AND ABTC.TIPO_CUENTA_ID    = ATC.ID_TIPO_CUENTA
    AND ATC.DESCRIPCION_CUENTA = 'TARJETA VISA'
    AND ATC.ESTADO             = 'Activo'
    AND AB.DESCRIPCION_BANCO   = 'BANCO MACHALA'
    AND AB.ESTADO              = 'Activo') AND USR_CREACION='icromero' AND EMPRESA_COD ='18';
    
/**
 * DEBE EJECUTARSE EN DB_FINANCIERO
 * Script se elimina formato de respuesta para tarjetas mastercard banco machala 
 * @author Ivan Romero <icromero@telconet.ec>
 * @version 1.0 22-03-2021 - Version Inicial.
 */
Delete from DB_FINANCIERO.admi_formato_debito_respuesta where BANCO_TIPO_CUENTA_ID=(SELECT ABTC.ID_BANCO_TIPO_CUENTA
    FROM DB_FINANCIERO.ADMI_BANCO_TIPO_CUENTA ABTC,DB_FINANCIERO.ADMI_BANCO AB,DB_FINANCIERO.ADMI_TIPO_CUENTA ATC
    WHERE ABTC.BANCO_ID        = AB.ID_BANCO
    AND ABTC.TIPO_CUENTA_ID    = ATC.ID_TIPO_CUENTA
    AND ATC.DESCRIPCION_CUENTA = 'TARJETA MASTERCARD'
    AND ATC.ESTADO             = 'Activo'
    AND AB.DESCRIPCION_BANCO   = 'BANCO MACHALA'
    AND AB.ESTADO              = 'Activo') AND USR_CREACION='icromero' AND EMPRESA_COD ='18';
    

/**
 * DEBE EJECUTARSE EN DB_FINANCIERO
 * Script para eliminar detalle de formato de carga Banco Machala  tarjetas mastercard
 * @author Ivan Romero <icromero@telconet.ec>
 * @version 1.0 22-03-2021 - Version Inicial.
 */
Delete from DB_FINANCIERO.ADMI_FORMATO_DEBITO where BANCO_TIPO_CUENTA_ID = (SELECT ABTC.ID_BANCO_TIPO_CUENTA
    FROM DB_FINANCIERO.ADMI_BANCO_TIPO_CUENTA ABTC,DB_FINANCIERO.ADMI_BANCO AB,DB_FINANCIERO.ADMI_TIPO_CUENTA ATC
    WHERE ABTC.BANCO_ID        = AB.ID_BANCO
    AND ABTC.TIPO_CUENTA_ID    = ATC.ID_TIPO_CUENTA
    AND ATC.DESCRIPCION_CUENTA = 'TARJETA MASTERCARD'
    AND ATC.ESTADO             = 'Activo'
    AND AB.DESCRIPCION_BANCO   = 'BANCO MACHALA'
    AND AB.ESTADO              = 'Activo') AND USR_CREACION = 'icromero' AND EMPRESA_COD ='18';
/**
 * DEBE EJECUTARSE EN DB_FINANCIERO
 * Script para eliminar detalle de formato de carga Banco Machala  tarjetas visa
 * @author Ivan Romero <icromero@telconet.ec>
 * @version 1.0 22-03-2021 - Version Inicial.
 */
Delete from DB_FINANCIERO.ADMI_FORMATO_DEBITO where BANCO_TIPO_CUENTA_ID = (SELECT ABTC.ID_BANCO_TIPO_CUENTA
    FROM DB_FINANCIERO.ADMI_BANCO_TIPO_CUENTA ABTC,DB_FINANCIERO.ADMI_BANCO AB,DB_FINANCIERO.ADMI_TIPO_CUENTA ATC
    WHERE ABTC.BANCO_ID        = AB.ID_BANCO
    AND ABTC.TIPO_CUENTA_ID    = ATC.ID_TIPO_CUENTA
    AND ATC.DESCRIPCION_CUENTA = 'TARJETA VISA'
    AND ATC.ESTADO             = 'Activo'
    AND AB.DESCRIPCION_BANCO   = 'BANCO MACHALA'
    AND AB.ESTADO              = 'Activo') AND USR_CREACION = 'icromero' AND EMPRESA_COD ='18';
    
/**
 * DEBE EJECUTARSE EN DB_FINANCIERO
 * Script para eliminar cabecera de formato de carga Banco Machala  tarjetas mastercard
 * @author Ivan Romero <icromero@telconet.ec>
 * @version 1.0 22-03-2021 - Version Inicial.
 */
Delete from DB_FINANCIERO.ADMI_NOMBRE_ARCHIVO_EMPRESA where BANCO_TIPO_CUENTA_ID = (SELECT ABTC.ID_BANCO_TIPO_CUENTA
    FROM DB_FINANCIERO.ADMI_BANCO_TIPO_CUENTA ABTC,DB_FINANCIERO.ADMI_BANCO AB,DB_FINANCIERO.ADMI_TIPO_CUENTA ATC
    WHERE ABTC.BANCO_ID        = AB.ID_BANCO
    AND ABTC.TIPO_CUENTA_ID    = ATC.ID_TIPO_CUENTA
    AND ATC.DESCRIPCION_CUENTA = 'TARJETA MASTERCARD'
    AND ATC.ESTADO             = 'Activo'
    AND AB.DESCRIPCION_BANCO   = 'BANCO MACHALA'
    AND AB.ESTADO              = 'Activo') AND USR_CREACION = 'icromero' AND EMPRESA_COD ='18';

/**
 * DEBE EJECUTARSE EN DB_FINANCIERO
 * Script para eliminar cabecera de formato de carga Banco Machala  tarjetas visa
 * @author Ivan Romero <icromero@telconet.ec>
 * @version 1.0 22-03-2021 - Version Inicial.
 */
Delete from DB_FINANCIERO.ADMI_NOMBRE_ARCHIVO_EMPRESA where BANCO_TIPO_CUENTA_ID = (SELECT ABTC.ID_BANCO_TIPO_CUENTA
    FROM DB_FINANCIERO.ADMI_BANCO_TIPO_CUENTA ABTC,DB_FINANCIERO.ADMI_BANCO AB,DB_FINANCIERO.ADMI_TIPO_CUENTA ATC
    WHERE ABTC.BANCO_ID        = AB.ID_BANCO
    AND ABTC.TIPO_CUENTA_ID    = ATC.ID_TIPO_CUENTA
    AND ATC.DESCRIPCION_CUENTA = 'TARJETA VISA'
    AND ATC.ESTADO             = 'Activo'
    AND AB.DESCRIPCION_BANCO   = 'BANCO MACHALA'
    AND AB.ESTADO              = 'Activo') AND USR_CREACION = 'icromero' AND EMPRESA_COD ='18';

/**
 * DEBE EJECUTARSE EN DB_FINANCIERO
 * Script para actualizar grupo  para banco del austro debito especial visa
 * @author Ivan Romero <icromero@telconet.ec>
 * @version 1.0 22-03-2021 - Version Inicial.
 */
update DB_FINANCIERO.ADMI_GRUPO_ARCHIVO_DEBITO_CAB set NOMBRE_GRUPO ='Tarjetas Visa otros bancos (Debito Especial)' where NOMBRE_GRUPO ='Tarjetas Visa (Debito Especial Del Austro)' and EMPRESA_COD='18';
/**
 * DEBE EJECUTARSE EN DB_FINANCIERO
 * Script para actualizar grupo  para banco del austro debito especial mastercard
 * @author Ivan Romero <icromero@telconet.ec>
 * @version 1.0 22-03-2021 - Version Inicial.
 */
update DB_FINANCIERO.ADMI_GRUPO_ARCHIVO_DEBITO_CAB set NOMBRE_GRUPO ='Tarjetas Mastercard otros bancos (Debito Especial)' where NOMBRE_GRUPO ='Tarjetas Mastercard (Debito Especial Del Austro)' and EMPRESA_COD='18';

/**
 * DEBE EJECUTARSE EN DB_FINANCIERO
 * Script para eliminar grupo  para banco machala debito especial visa
 * @author Ivan Romero <icromero@telconet.ec>
 * @version 1.0 22-03-2021 - Version Inicial.
 */
Delete from DB_FINANCIERO.ADMI_GRUPO_ARCHIVO_DEBITO_CAB where BANCO_TIPO_CUENTA_ID =(SELECT ABTC.ID_BANCO_TIPO_CUENTA
    FROM DB_FINANCIERO.ADMI_BANCO_TIPO_CUENTA ABTC,DB_FINANCIERO.ADMI_BANCO AB,DB_FINANCIERO.ADMI_TIPO_CUENTA ATC
    WHERE ABTC.BANCO_ID        = AB.ID_BANCO
    AND ABTC.TIPO_CUENTA_ID    = ATC.ID_TIPO_CUENTA
    AND ATC.DESCRIPCION_CUENTA = 'TARJETA VISA'
    AND ATC.ESTADO             = 'Activo'
    AND AB.DESCRIPCION_BANCO   = 'BANCO MACHALA'
    AND AB.ESTADO              = 'Activo') AND USR_CREACION = 'icromero' AND EMPRESA_COD ='18';
    
/**
 * DEBE EJECUTARSE EN DB_FINANCIERO
 * Script para eliminar grupo  para banco machala debito especial mastercard
 * @author Ivan Romero <icromero@telconet.ec>
 * @version 1.0 22-03-2021 - Version Inicial.
 */
Delete from DB_FINANCIERO.ADMI_GRUPO_ARCHIVO_DEBITO_CAB where BANCO_TIPO_CUENTA_ID =(SELECT ABTC.ID_BANCO_TIPO_CUENTA
    FROM DB_FINANCIERO.ADMI_BANCO_TIPO_CUENTA ABTC,DB_FINANCIERO.ADMI_BANCO AB,DB_FINANCIERO.ADMI_TIPO_CUENTA ATC
    WHERE ABTC.BANCO_ID        = AB.ID_BANCO
    AND ABTC.TIPO_CUENTA_ID    = ATC.ID_TIPO_CUENTA
    AND ATC.DESCRIPCION_CUENTA = 'TARJETA MASTERCARD'
    AND ATC.ESTADO             = 'Activo'
    AND AB.DESCRIPCION_BANCO   = 'BANCO MACHALA'
    AND AB.ESTADO              = 'Activo') AND USR_CREACION = 'icromero' AND EMPRESA_COD ='18';

/**
 * DEBE EJECUTARSE EN DB_FINANCIERO
 * Script para eliminar CARACTERISTICA para banco machala debito especial mastercard
 * @author Ivan Romero <icromero@telconet.ec>
 * @version 1.0 16-04-2021 - Version Inicial.
 */
DELETE from  DB_FINANCIERO.ADMI_FORMATO_DEBITO_CARACT 
WHERE BANCO_TIPO_CUENTA_ID = (SELECT Id_Banco_Tipo_Cuenta fROM DB_FINANCIERO.ADMI_BANCO_TIPO_CUENTA where Banco_Id = (SELECT id_banco FROM DB_FINANCIERO.ADMI_BANCO where Descripcion_Banco = 'BANCO MACHALA') and Tipo_Cuenta_Id =(SELECT Id_Tipo_Cuenta fROM DB_FINANCIERO.ADMI_TIPO_CUENTA where Descripcion_Cuenta ='TARJETA MASTERCARD') )
AND USR_CREACION = 'icromero' AND EMPRESA_COD = '18';

/**
 * DEBE EJECUTARSE EN DB_FINANCIERO
 * Script para eliminar CARACTERISTICA para banco machala debito especial mastercard
 * @author Ivan Romero <icromero@telconet.ec>
 * @version 1.0 16-04-2021 - Version Inicial.
 */
DELETE from  DB_FINANCIERO.ADMI_FORMATO_DEBITO_CARACT 
WHERE BANCO_TIPO_CUENTA_ID = (SELECT Id_Banco_Tipo_Cuenta fROM DB_FINANCIERO.ADMI_BANCO_TIPO_CUENTA where Banco_Id = (SELECT id_banco FROM DB_FINANCIERO.ADMI_BANCO where Descripcion_Banco = 'BANCO MACHALA') and Tipo_Cuenta_Id =(SELECT Id_Tipo_Cuenta fROM DB_FINANCIERO.ADMI_TIPO_CUENTA where Descripcion_Cuenta ='TARJETA VISA') )
AND USR_CREACION = 'icromero' AND EMPRESA_COD = '18';

/**
 * DEBE EJECUTARSE EN DB_FINANCIERO
 * Script para reversar creacion de caracteristica IVA_BIENES_SERVICIO_CERO
 * @author Ivan Romero <icromero@telconet.ec>
 * @version 1.0 25-02-2021 - Version Inicial.
 */

DELETE FROM DB_COMERCIAL.ADMI_CARACTERISTICA 
WHERE DESCRIPCION_CARACTERISTICA ='IVA_BIENES_SERVICIOS_CERO' AND ESTADO ='Activo' AND USR_CREACION = 'icromero';


/**
 * DEBE EJECUTARSE EN DB_FINANCIERO
 * Script para reversar asignacion caracteristica IVA_BIENES_SERVICIO_CERO al formato Banco Produbanco - TN
 * @author Ivan Romero <icromero@telconet.ec>
 * @version 1.0 25-02-2021 - Version Inicial.
 */

DELETE FROM DB_FINANCIERO.ADMI_FORMATO_DEBITO_CARACT 
WHERE CARACTERISTICA_ID =(select ID_CARACTERISTICA from DB_COMERCIAL.ADMI_CARACTERISTICA WHERE DESCRIPCION_CARACTERISTICA ='IVA_BIENES_SERVICIOS_CERO' AND ESTADO ='Activo' AND USR_CREACION = 'icromero')
AND BANCO_TIPO_CUENTA_ID =(SELECT Id_Banco_Tipo_Cuenta fROM DB_FINANCIERO.ADMI_BANCO_TIPO_CUENTA where Banco_Id = (SELECT id_banco FROM DB_FINANCIERO.ADMI_BANCO where Descripcion_Banco = 'BANCO PRODUBANCO-PROMERICA') and Tipo_Cuenta_Id =(SELECT Id_Tipo_Cuenta fROM DB_FINANCIERO.ADMI_TIPO_CUENTA where Descripcion_Cuenta ='AHORRO') )
AND EMPRESA_COD ='10';


/**
 * DEBE EJECUTARSE EN DB_FINANCIERO
 * Script para reversar creacion de caracteristica REFERENCIA_SIN_FORMATO
 * @author Ivan Romero <icromero@telconet.ec>
 * @version 1.0 06-05-2021 - Version Inicial.
 */

DELETE FROM DB_COMERCIAL.ADMI_CARACTERISTICA 
WHERE DESCRIPCION_CARACTERISTICA ='REFERENCIA_SIN_FORMATO' AND ESTADO ='Activo' AND USR_CREACION = 'icromero';


/**
 * DEBE EJECUTARSE EN DB_FINANCIERO
 * Script para reversar asignacion caracteristica REFERENCIA_SIN_FORMATO al formato Banco Machala Mastercard - TN
 * @author Ivan Romero <icromero@telconet.ec>
 * @version 1.0 06-05-2021 - Version Inicial.
 */

DELETE FROM DB_FINANCIERO.ADMI_FORMATO_DEBITO_CARACT 
WHERE CARACTERISTICA_ID =(select ID_CARACTERISTICA from DB_COMERCIAL.ADMI_CARACTERISTICA WHERE DESCRIPCION_CARACTERISTICA ='REFERENCIA_SIN_FORMATO' AND ESTADO ='Activo' AND USR_CREACION = 'icromero')
AND BANCO_TIPO_CUENTA_ID =(SELECT ID_BANCO_TIPO_CUENTA fROM DB_FINANCIERO.ADMI_BANCO_TIPO_CUENTA WHERE BANCO_ID = (SELECT id_banco FROM DB_FINANCIERO.ADMI_BANCO WHERE DESCRIPCION_BANCO = 'BANCO MACHALA') and TIPO_CUENTA_ID =(SELECT ID_TIPO_CUENTA fROM DB_FINANCIERO.ADMI_TIPO_CUENTA WHERE DESCRIPCION_CUENTA ='TARJETA MASTERCARD') )
AND EMPRESA_COD ='18';

/**
 * DEBE EJECUTARSE EN DB_FINANCIERO
 * Script para reversar asignacion caracteristica REFERENCIA_SIN_FORMATO al formato Banco Machala Visa - TN
 * @author Ivan Romero <icromero@telconet.ec>
 * @version 1.0 06-05-2021 - Version Inicial.
 */

DELETE FROM DB_FINANCIERO.ADMI_FORMATO_DEBITO_CARACT 
WHERE CARACTERISTICA_ID =(select ID_CARACTERISTICA from DB_COMERCIAL.ADMI_CARACTERISTICA WHERE DESCRIPCION_CARACTERISTICA ='REFERENCIA_SIN_FORMATO' AND ESTADO ='Activo' AND USR_CREACION = 'icromero')
AND BANCO_TIPO_CUENTA_ID =(SELECT ID_BANCO_TIPO_CUENTA fROM DB_FINANCIERO.ADMI_BANCO_TIPO_CUENTA WHERE BANCO_ID = (SELECT id_banco FROM DB_FINANCIERO.ADMI_BANCO WHERE DESCRIPCION_BANCO = 'BANCO MACHALA') and TIPO_CUENTA_ID =(SELECT ID_TIPO_CUENTA fROM DB_FINANCIERO.ADMI_TIPO_CUENTA WHERE DESCRIPCION_CUENTA ='TARJETA VISA') )
AND EMPRESA_COD ='18';

COMMIT;