/**
 * @author Alex Arrega <atarreaga@telconet.ec>
 * @version 1.0
 * @since 19-06-2019    
 * Se crea la sentencia DML para la eliminación de la asociación 'FinancieroWSController' con el usuario 'WS-RDA'.
 */

--ELIMINACIÓN DE LA ASOCIACIÓN 'FinancieroWSController' CON EL USUARIO 'WS-RDA'
DELETE DB_TOKENSECURITY.WEB_SERVICE WHERE SERVICE = 'FinancieroWSController';

  COMMIT;
/
  