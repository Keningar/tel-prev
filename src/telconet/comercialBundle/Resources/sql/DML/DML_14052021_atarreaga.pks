/**
 * @author Alex Arreaga <atarreaga@telconet.ec>
 * @version 1.0
 * @since 14-05-2021  
 * Se crea DML para regularizar información en nombres, apellidos con otros caracteres
 * especiales de la tabla DB_COMERCIAL.INFO_PERSONA.
 */

UPDATE DB_COMERCIAL.INFO_PERSONA Y
SET Y.NOMBRES       = Y.NOMBRES, 
    Y.APELLIDOS     = Y.APELLIDOS,
    Y.RAZON_SOCIAL  = Y.RAZON_SOCIAL
WHERE 
Y.ID_PERSONA = (SELECT X.ID_PERSONA
                FROM  DB_COMERCIAL.INFO_PERSONA X
                WHERE  X.ID_PERSONA = Y.ID_PERSONA
                AND X.ID_PERSONA IN (1882628,1885447,1888960));

COMMIT;
/
