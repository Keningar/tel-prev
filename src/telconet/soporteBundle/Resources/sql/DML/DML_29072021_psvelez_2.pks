/**
 * DEBE EJECUTARSE EN DB_COMERCIAL
 * Script para crear de registro para web service de consulta 
 * de motivo de cierre de casos Hal
 * @author Pero Velez <psvelez@telconet.ec>
 * @version 1.0 28-07-2021 - Versión Inicial.
 */

insert into DB_COMERCIAL.admi_caracteristica s
values(DB_COMERCIAL.seq_admi_caracteristica.nextval,'HIPOTESIS_CIERRE_CASO_HAL',
       'T','Activo',sysdate,'psvelez',null,null,'SOPORTE',null);

COMMIT;
/