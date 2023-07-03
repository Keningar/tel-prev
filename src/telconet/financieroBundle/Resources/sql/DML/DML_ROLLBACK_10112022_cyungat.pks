/** 
 * @author Christian Yunga <cyungat@telconet.ec>
 * @version 1.0 
 * @since 10-11-2022 
 * Se crea DML de reverso de configuraciones del Proyecto Tarjetas ABU.
 */


DELETE FROM DB_COMUNICACION.ADMI_PLANTILLA ap 
WHERE ap.CODIGO = 'ACT_TAR_ABU';

COMMIT;
/

