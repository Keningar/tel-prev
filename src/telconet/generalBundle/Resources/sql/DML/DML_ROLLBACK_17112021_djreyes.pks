-- Eliminar detalles - Elimina todos los paramtetros del formulario
DELETE FROM DB_GENERAL.ADMI_PARAMETRO_DET
WHERE PARAMETRO_ID = (
        SELECT ID_PARAMETRO
        FROM DB_GENERAL.ADMI_PARAMETRO_CAB
        WHERE NOMBRE_PARAMETRO = 'LISTADO_VALORES_PRODUCTOS_TV'
    )
AND VALOR1 = 'GTVPREMIUM' AND USR_CREACION = 'djreyes';

-- Eliminar cabecera - Nueva cabecera para datos del formulario
DELETE FROM DB_GENERAL.ADMI_PARAMETRO_CAB
WHERE NOMBRE_PARAMETRO = 'LISTADO_VALORES_PRODUCTOS_TV'
AND PROCESO = 'PRODUCTOS_TV' AND USR_CREACION = 'djreyes';

COMMIT;
/