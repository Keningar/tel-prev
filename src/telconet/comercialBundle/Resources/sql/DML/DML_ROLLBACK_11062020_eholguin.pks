/**
 * @author Edgar Holguín <eholguin@telconet.ec>
 * @version 1.0
 * @since 11-06-2020    
 */

DELETE FROM DB_COMUNICACION.INFO_ALIAS_PLANTILLA
WHERE PLANTILLA_ID = (SELECT CAB.ID_PLANTILLA
                      FROM DB_COMUNICACION.ADMI_PLANTILLA CAB
                      WHERE CAB.CODIGO        = 'RPT_FACTINST'
                      AND   CAB.USR_CREACION  = 'eholguin' );

DELETE FROM DB_COMUNICACION.ADMI_PLANTILLA
WHERE CODIGO              = 'RPT_FACTINST'
AND USR_CREACION          = 'eholguin';

COMMIT;
/
