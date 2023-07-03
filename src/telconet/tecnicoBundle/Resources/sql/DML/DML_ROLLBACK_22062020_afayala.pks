SET DEFINE OFF;

-- Eliminar en la tabla ADMI_PARAMETRO_DET
DELETE FROM DB_GENERAL.ADMI_PARAMETRO_DET
WHERE PARAMETRO_ID = (SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'REQUIERE_TRABAJO_CABLEADO_ESTRUCTURADO')
AND DESCRIPCION = 'FIBRA';

-- Eliminar en la tabla ADMI_PARAMETRO_DET
DELETE FROM DB_GENERAL.ADMI_PARAMETRO_DET
WHERE PARAMETRO_ID = (SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'REQUIERE_TRABAJO_CABLEADO_ESTRUCTURADO')
AND DESCRIPCION = 'Obras Civiles';

-- Eliminar en la tabla ADMI_PARAMETRO_CAB
DELETE FROM DB_GENERAL.ADMI_PARAMETRO_CAB
WHERE NOMBRE_PARAMETRO = 'REQUIERE_TRABAJO_CABLEADO_ESTRUCTURADO';

-- Eliminar en la tabla ADMI_PARAMETRO_DET
DELETE FROM DB_GENERAL.ADMI_PARAMETRO_DET
WHERE PARAMETRO_ID = (SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'REQUIERE_FLUJO')
AND VALOR1 = 'Cableado Estructurado';

-- Eliminar en la tabla ADMI_PARAMETRO_CAB
DELETE FROM DB_GENERAL.ADMI_PARAMETRO_CAB
WHERE NOMBRE_PARAMETRO = 'REQUIERE_FLUJO';

-- Eliminar Relación entre Producto y Característica
DELETE FROM DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA
WHERE PRODUCTO_ID = (SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO WHERE DESCRIPCION_PRODUCTO = 'Cableado Estructurado' AND ESTADO = 'Activo')
AND CARACTERISTICA_ID = (SELECT ID_CARACTERISTICA FROM DB_COMERCIAL.ADMI_CARACTERISTICA WHERE DESCRIPCION_CARACTERISTICA = 'REQUIERE TRABAJO' 
AND ESTADO = 'Activo');

-- Eliminar Característica Instalación Simultánea
DELETE FROM DB_COMERCIAL.ADMI_CARACTERISTICA
WHERE DESCRIPCION_CARACTERISTICA = 'REQUIERE TRABAJO';

-- Actualizar campo ESTADO_INICIAL para producto Cableado Estructurado
UPDATE DB_COMERCIAL.ADMI_PRODUCTO SET ESTADO_INICIAL = 'Pendiente' 
WHERE DESCRIPCION_PRODUCTO = 'Cableado Estructurado' AND ESTADO = 'Activo';

UPDATE DB_COMERCIAL.ADMI_PRODUCTO
SET ES_ENLACE = 'NO'
WHERE ID_PRODUCTO = 1116;

COMMIT;

/