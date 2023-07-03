
/** 
 * @author Anabelle Penaherrera <apenaherrera@telconet.ec>
 * @version 1.0 
 * @since 18-07-2022
 * Se crea DML Rollback de parámetro mensaje para validación en la clonación de promociones.
 */
 
DELETE FROM DB_GENERAL.ADMI_PARAMETRO_DET WHERE PARAMETRO_ID = (
        SELECT ID_PARAMETRO 
        FROM DB_GENERAL.ADMI_PARAMETRO_CAB
        WHERE NOMBRE_PARAMETRO = 'PROM_PARAMETROS'
        AND ESTADO             = 'Activo'
    ) AND DESCRIPCION = 'MENSAJE_VALIDACION_CLONA_PROMO';
    
    
COMMIT;
/
