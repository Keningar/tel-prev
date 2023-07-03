/**
 * Documentación para 'DML_ROLLBACK_19072021_afayala.pks'
 * Pks que permite realizar reversos de inserción
 *
 * @author Antonio Ayala <afayala@telconet.ec>
 * @version 1.0 19-07-2021
 */

SET DEFINE OFF;

-- Eliminar en la tabla INFO_ALIAS_PLANTILLA
DELETE FROM DB_COMUNICACION.INFO_ALIAS_PLANTILLA
WHERE PLANTILLA_ID = (SELECT ID_PLANTILLA FROM DB_COMUNICACION.ADMI_PLANTILLA WHERE NOMBRE_PLANTILLA = 'Notificación de Finalizacion en el servicio de Seguridad'
       AND CODIGO = 'SEGURIDAD_CPE');

-- Eliminar en la tabla ADMI_PLANTILLA
DELETE FROM DB_COMUNICACION.ADMI_PLANTILLA
WHERE CODIGO = 'SEGURIDAD_CPE';

COMMIT;

/
