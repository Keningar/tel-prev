GRANT SELECT ON DB_GENERAL.ADMI_GESTION_DIRECTORIOS TO DB_INFRAESTRUCTURA;
GRANT READ, WRITE ON DIRECTORY RESPSOLARIS TO DB_INFRAESTRUCTURA;
GRANT READ ON DIRECTORY RESPSOLARIS TO DB_GENERAL;
/
--AGREGAR PERMISOS DE FUNCIONALIDADES JAVA A USUARIO DB_GENERAL PARA PODER EJECUTAR ENVIO DE ARCHIVOS
begin
  dbms_java.grant_permission
      ('DB_GENERAL',
       'java.io.FilePermission',
       '<<ALL FILES>>',
       'execute');
  dbms_java.grant_permission
      ('DB_GENERAL',
       'java.lang.RuntimePermission',
       '*',
       'writeFileDescriptor' );
end;
/
exec dbms_java.grant_permission('DB_GENERAL', 'SYS:java.net.SocketPermission', '192.168.100.62:80', 'resolve,connect');
exec dbms_java.grant_permission('DB_GENERAL', 'SYS:java.io.FilePermission', '<<ALL FILES>>', 'read');
/