/**
 * DEBE EJECUTARSE EN DB_GENERAL
 * Script para rollback del archivo DML_13092022_psvelez_2.pks
 * @author Pero Velez <psvelez@telconet.ec>
 * @version 1.0 10-09-2022 - Versión Inicial.
 */

DELETE DB_GENERAL.ADMI_GESTION_DIRECTORIOS AGD 
 where AGD.empresa='TN' 
   and AGD.modulo='Empleado' 
   and AGD.APLICACION = 'Naf'
   and AGD.SUBMODULO = 'Fotos'; 

DELETE DB_GENERAL.ADMI_PARAMETRO_DET s 
 where s.DESCRIPCION = 'URL_NFS_GUARDAR_ARCHIVO';

DELETE DB_GENERAL.ADMI_PARAMETRO_DET s 
 where s.DESCRIPCION = 'PATH_URL_PUBLICA';

DELETE DB_GENERAL.ADMI_PARAMETRO_CAB s 
 where s.NOMBRE_PARAMETRO   ='URL_MICROSERVICIO';


COMMIT;
/
