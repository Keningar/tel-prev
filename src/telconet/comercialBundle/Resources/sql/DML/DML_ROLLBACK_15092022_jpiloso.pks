/**
 * DEBE EJECUTARSE EN DB_GENERAL
 * Script para eliminar los parametros del filtro de los estados en promociones de franja horaria
 * @author Jessenia Piloso <jpiloso@telconet.ec>
 * @version 1.0
 * @since 15-09-2022
 */

DECLARE

BEGIN

DELETE FROM DB_GENERAL.ADMI_PARAMETRO_DET WHERE PARAMETRO_ID = (
        SELECT ID_PARAMETRO 
        FROM DB_GENERAL.ADMI_PARAMETRO_CAB
        WHERE NOMBRE_PARAMETRO = 'PARAMETROS_PROMOCIONES_MASIVAS_BW'
        AND ESTADO             = 'Activo'
    ) AND DESCRIPCION = 'ESTADOS PERMITIDO PROMO FRANJA HORARIA';

COMMIT;

END;

/