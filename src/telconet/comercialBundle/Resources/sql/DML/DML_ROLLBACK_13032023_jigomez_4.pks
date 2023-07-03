/**
 * Script para eliminar características relacionada MAC
 * @author Jorge Gomez <jigomez@telconet.ec>
 * @version 1.0 13-03-2023 - Versión Inicial.
 */

    DELETE FROM DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA WHERE PRODUCTO_ID = 1099 AND CARACTERISTICA_ID = 6;

	COMMIT;
/