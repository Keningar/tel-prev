/**
 * DEBE EJECUTARSE EN DB_COMERCIAL
 * Script para reverso de script DML_22020407_psvelez_2.pks
 * @author Pedro Velez <psvelez@telconet.ec>
 * @version 1.0 25-03-2022 - Versión Inicial.
 */

DELETE from DB_COMERCIAL.ADMI_CARACTERISTICA a
WHERE a.DESCRIPCION_CARACTERISTICA ='PUSH_ID_CLIENTE';
commit;
/
