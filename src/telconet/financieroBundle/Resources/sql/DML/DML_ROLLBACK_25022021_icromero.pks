/**
 * DEBE EJECUTARSE EN DB_FINANCIERO
 * Script para reversar actualizacion en el formato Banco Produbanco - TN
 * @author Ivan Romero <icromero@telconet.ec>
 * @version 1.0 25-01-2021 - Version Inicial.
 */

UPDATE DB_FINANCIERO.ADMI_FORMATO_DEBITO SET OPERACION_ADICIONAL = 'verificacodigoretencion|Bien_Base|NA|NA'
WHERE ID_FORMATO_DEBITO = 1405;
COMMIT;


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
