DELETE FROM DB_GENERAL.ADMI_GESTION_DIRECTORIOS WHERE CODIGO_APP = 4 AND APLICACION = 'TelcosWeb' 
AND MODULO = 'Soporte' AND SUBMODULO LIKE '%GestionDocumentos%';
--
--
DELETE FROM DB_GENERAL.ADMI_GESTION_DIRECTORIOS WHERE CODIGO_APP = 4 AND APLICACION = 'TelcosWeb' 
AND MODULO = 'Soporte' AND SUBMODULO = 'InformeEjecutivo';
--
--
COMMIT;

/
