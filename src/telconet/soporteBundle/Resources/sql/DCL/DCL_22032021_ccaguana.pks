
--AGREGAR PERMISOS DE FUNCIONALIDADES JAVA A USUARIO DB_FINANCIERO
--PARA PODER EJECUTAR ENVIAR DE ARCHIVOS
begin
  dbms_java.grant_permission
      ('DB_FINANCIERO',
       'java.io.FilePermission',
       '<<ALL FILES>>',
       'execute');
  dbms_java.grant_permission
      ('DB_FINANCIERO',
       'java.lang.RuntimePermission',
       '*',
       'writeFileDescriptor' );
end;

/

exec dbms_java.grant_permission( 'DB_FINANCIERO','SYS:java.net.SocketPermission', '192.168.100.62:80', 'resolve,connect');
exec dbms_java.grant_permission('DB_FINANCIERO', 'SYS:java.io.FilePermission', '<<ALL FILES>>','read');

/