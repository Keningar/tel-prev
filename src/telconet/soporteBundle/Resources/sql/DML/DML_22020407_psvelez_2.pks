/**
 * DEBE EJECUTARSE EN DB_COMERCIAL
 * Script para crear caracteristica de clientes megadatos
 * push-id
 * @author Pedro Velez <psvelez@telconet.ec>
 * @version 1.0 08-04-2022 - Versión Inicial.
 */

 INSERT INTO DB_COMERCIAL.ADMI_CARACTERISTICA 
values(DB_COMERCIAL.SEQ_ADMI_CARACTERISTICA.NEXTVAL,
'PUSH_ID_CLIENTE',
'C',
'Activo',
sysdate,
'psvelez',
NULL,
NULL,
'SOPORTE',
NULL);

commit;
/
