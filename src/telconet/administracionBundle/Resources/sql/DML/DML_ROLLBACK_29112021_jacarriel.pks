/*
 *
 * Se elimina nuevo parametro de dias de bloqueo de bobinas para cuadrillas satelites.
 *	 
 * @author Jeampier Carriel <jacarriel@telconet.ec>
 * @version 1.0 29-11-2021
 *
*/

DELETE FROM DB_GENERAL.ADMI_PARAMETRO_DET DETALLE
WHERE
    PARAMETRO_ID = (
        SELECT
            ID_PARAMETRO
        FROM
            DB_GENERAL.ADMI_PARAMETRO_CAB
        WHERE
            NOMBRE_PARAMETRO = 'PARAMETROS_GENERALES_MOVIL'
    )
    AND VALOR1 = 'DIAS_BLOQUEO_BOBINA_DESPACHO_SATELITE';

--------------------------------------------------------------------------
-- Se elimina la nueva columna ES_SATELITE en la tabla ADMI_CUADRILLA
--------------------------------------------------------------------------

ALTER TABLE DB_COMERCIAL.ADMI_CUADRILLA
  DROP COLUMN Es_Satelite;


COMMIT;