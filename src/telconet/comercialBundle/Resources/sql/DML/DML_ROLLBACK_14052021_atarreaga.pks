/**
 * @author Alex Arreaga <atarreaga@telconet.ec>
 * @version 1.0
 * @since 14-05-2021  
 * Se crea DML para reversar regularización de información en nombres, apellidos con caracteres
 * especiales de la tabla DB_COMERCIAL.INFO_PERSONA.
 */

UPDATE DB_COMERCIAL.INFO_PERSONA SET 
NOMBRES      = 'ZOILA CECILIA', 
APELLIDOS    = 'NUELA  ESCOBAR'
WHERE
ID_PERSONA = 1882628;

UPDATE DB_COMERCIAL.INFO_PERSONA SET 
NOMBRES   = 'HECTOR ROLANDO  ', 
APELLIDOS = 'CERCADO BURGOS'
WHERE
ID_PERSONA = 1885447; 

UPDATE DB_COMERCIAL.INFO_PERSONA SET 
NOMBRES   = 'TITO DANIEL ', 
APELLIDOS = 'CASTRO REYES'
WHERE
ID_PERSONA = 1888960;

COMMIT;
/
