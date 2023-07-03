/** 
 * @author José Candelario <jcandelario@telconet.ec>
 * @version 1.0 
 * @since 09-08-2022  
 * Se realiza modificación del tamaño del campo SERIE_FISICO en la tabla NAF47_TNET.MIGRA_ARCKMM.
 */


ALTER TABLE NAF47_TNET.MIGRA_ARCKMM MODIFY SERIE_FISICO VARCHAR2(18);
/
