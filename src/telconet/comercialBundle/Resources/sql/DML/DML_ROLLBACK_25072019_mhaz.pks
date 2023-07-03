/**
 * @author Edgar Holguín <eholguin@telconet.ec>
 * @version 1.0
 * @since 27-09-2019    
 */

DELETE FROM DB_COMUNICACION.INFO_ALIAS_PLANTILLA
WHERE PLANTILLA_ID = (SELECT CAB.ID_PLANTILLA
                      FROM DB_COMUNICACION.ADMI_PLANTILLA CAB
                      WHERE CAB.CODIGO        = 'CAMB_FORMPAG'
                      AND   CAB.USR_CREACION  = 'mhaz' );

DELETE FROM DB_COMUNICACION.ADMI_PLANTILLA
WHERE CODIGO              = 'CAMB_FORMPAG'
AND USR_CREACION          = 'mhaz';

DELETE FROM DB_COMUNICACION.ADMI_PLANTILLA
WHERE CODIGO              = 'CFP_CLT'
AND USR_CREACION          = 'eholguin';

COMMIT;
/
