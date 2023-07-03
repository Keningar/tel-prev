/*
 *
 * Se devuelven a estado Activo las cuadrillas prestadas a coordinadores.
 *	 
 * @author José Castillo <jmcastillo@telconet.ec>
 * @version 1.0 04-14-2023
 *
*/

UPDATE DB_COMERCIAL.ADMI_CUADRILLA SET ESTADO='Activo' WHERE ES_HAL='S' and ESTADO='Prestado' and COORDINADOR_PRESTADO_ID IS NOT NULL;

COMMIT;
/
