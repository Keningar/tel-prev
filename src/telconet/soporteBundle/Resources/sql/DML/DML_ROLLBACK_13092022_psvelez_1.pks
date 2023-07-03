/**
 * DEBE EJECUTARSE EN DB_COMERCIAL
 * Script para rollback del archivo DML_13092022_psvelez_1.pks
 * @author Pero Velez <psvelez@telconet.ec>
 * @version 1.0 10-09-2022 - Versión Inicial.
 */

DELETE DB_COMERCIAL.admi_caracteristica s where s.descripcion_caracteristica = 'CODIGO_TRABAJO';
DELETE DB_COMERCIAL.admi_caracteristica s where s.descripcion_caracteristica = 'URL_FOTO_EMPLEADO';

COMMIT;
/