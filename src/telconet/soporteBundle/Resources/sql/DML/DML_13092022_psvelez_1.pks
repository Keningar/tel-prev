/**
 * DEBE EJECUTARSE EN DB_COMERCIAL
 * caracteristica para el codigo de trabajo de tareas de casos de soporte
 * @author Pero Velez <psvelez@telconet.ec>
 * @version 1.0 08-09-2022 - Versión Inicial.
 */

insert into DB_COMERCIAL.admi_caracteristica s
values(DB_COMERCIAL.seq_admi_caracteristica.nextval,'CODIGO_TRABAJO',
       'C','Activo',sysdate,'psvelez',null,null,'SOPORTE',null);

insert into DB_COMERCIAL.admi_caracteristica s
values(DB_COMERCIAL.seq_admi_caracteristica.nextval,'URL_FOTO_EMPLEADO',
       'C','Activo',sysdate,'psvelez',null,null,'SOPORTE',null);

COMMIT;
/