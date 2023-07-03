/**
 * DEBE EJECUTARSE EN DB_FINANCIERO
 * Script eliminar debito detalle para (formato machala) tarjeta mastercard, estos se cargan a elegir una opcion del comboBox Formatos de la pantalla Generar Debitos opcion Visa/Mastercard
 * @author Ivan Romero <icromero@telconet.ec>
 * @version 1.0 22-03-2021 - Version Inicial.
 */
delete from db_general.admi_parametro_det 
where usr_creacion='icromero' 
and Valor2 in('Mensaje','Parametro') 
or descripcion in ('ms.nfs.url','ms.nfs.submodulo','ms.nfs.modulo')
and parametro_id=(select id_parametro from db_general.admi_parametro_Cab where nombre_parametro='DEBITOS_PLANIFICADOS');

COMMIT;