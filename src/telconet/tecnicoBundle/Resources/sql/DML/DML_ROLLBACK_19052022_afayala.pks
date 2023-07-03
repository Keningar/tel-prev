/**
 * Documentación para eliminar submodulo para NFS
 *
 * @author Antonio Ayala <afayala@telconet.ec>
 * @version 1.0 19-05-2022
 */

-- Eliminar parámetro de almacenamiento NFS
DELETE FROM DB_GENERAL.ADMI_GESTION_DIRECTORIOS
  WHERE  CODIGO_APP = 4
    AND  APLICACION = 'TelcosWeb'
    AND  PAIS='593'
    AND  EMPRESA='MD'
    AND  MODULO='Tecnico'
    AND  SUBMODULO='SolicitudEquipo'
    AND  ESTADO='Activo';
    
COMMIT;

/

