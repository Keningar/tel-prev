/**
 *
 * Se crea script de rollback para el proyecto: de almacenamiento NFS
 *	 
 * @author Jonathan Mazon <jmazon@telconet.ec>
 * @version 1.0 22-04-2021
 */

--TN
DELETE FROM DB_GENERAL.ADMI_GESTION_DIRECTORIOS
  WHERE  CODIGO_APP = 4
    AND  CODIGO_PATH= 21
    AND  APLICACION = 'TelcosWeb'
    AND  PAIS='593'
    AND  EMPRESA='TN'
    AND  MODULO='Tecnico'
    AND  SUBMODULO='InspeccionRadio'
    AND  ESTADO='Activo';
--MD
DELETE  FROM DB_GENERAL.ADMI_GESTION_DIRECTORIOS
WHERE    CODIGO_APP = 4
    AND  CODIGO_PATH= 20
    AND  APLICACION = 'TelcosWeb'
    AND  PAIS='593'
    AND  EMPRESA='MD'
    AND  MODULO='Tecnico'
    AND  SUBMODULO='InspeccionRadio'
    AND  ESTADO='Activo';

COMMIT;

/
