/**
 * Documentación para modificar características para 
 * producto CLEAR CHANNEL PUNTO A PUNTO
 *
 * @author Josue Valencia <ajvalencia@telconet.ec>
 * @version 1.0 05-04-2023
 */


UPDATE DB_COMERCIAL.ADMI_PRODUCTO SET REQUIERE_INFO_TECNICA = 'NO', NOMBRE_TECNICO = 'OTROS',
CLASIFICACION = 'DATOS' 
WHERE DESCRIPCION_PRODUCTO = 'CLEAR CHANNEL PUNTO A PUNTO' AND ESTADO = 'Activo';


UPDATE DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA
SET VISIBLE_COMERCIAL = 'NO'
WHERE PRODUCTO_ID = (
        SELECT
            ID_PRODUCTO
        FROM
            DB_COMERCIAL.ADMI_PRODUCTO
        WHERE
            DESCRIPCION_PRODUCTO = 'CLEAR CHANNEL PUNTO A PUNTO' AND ESTADO = 'Activo')
     AND CARACTERISTICA_ID = (
        SELECT
            ID_CARACTERISTICA
        FROM
            DB_COMERCIAL.ADMI_CARACTERISTICA
        WHERE
            DESCRIPCION_CARACTERISTICA = 'ANCHO BANDA' AND ESTADO = 'Activo' )
    AND ESTADO = 'Activo';
    
UPDATE DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA
SET VISIBLE_COMERCIAL = 'NO'
WHERE PRODUCTO_ID = (
        SELECT
            ID_PRODUCTO
        FROM
            DB_COMERCIAL.ADMI_PRODUCTO
        WHERE
            DESCRIPCION_PRODUCTO = 'CLEAR CHANNEL PUNTO A PUNTO' AND ESTADO = 'Activo')
     AND CARACTERISTICA_ID = (
        SELECT
            ID_CARACTERISTICA
        FROM
            DB_COMERCIAL.ADMI_CARACTERISTICA
        WHERE
            DESCRIPCION_CARACTERISTICA = 'REQUIERE TRANSPORTE' AND ESTADO = 'Activo' )
    AND ESTADO = 'Activo';
    

COMMIT;

/