--Permisos de directorio para los archivos subidos desde la opción de cambio de plan masivo
GRANT READ, WRITE ON DIRECTORY DIR_PROCESOS_MASIVOS TO DB_INFRAESTRUCTURA;
GRANT READ ON DIRECTORY DIR_PROCESOS_MASIVOS TO DB_GENERAL;
/