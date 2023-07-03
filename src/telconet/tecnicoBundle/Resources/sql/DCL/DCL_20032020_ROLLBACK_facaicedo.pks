--=======================================================================
-- Reverso de la característica del id del elemento del cliente para las características del detalle de solicitud
-- Reverso de la característica del id de la interface del elemento del cliente para las características del detalle de solicitud
-- Reverso de la característica del id de la persona empresa rol para las características del detalle de solicitud
-- Reverso de la característica del id del producto del servicio para las características del detalle de solicitud
-- Reverso de la característica del tipo de recurso en las características del detalle de solicitud
--=======================================================================

-- REVERSO LA CARACTERISTICA 'ELEMENTO_CLIENTE_ID'
DELETE DB_COMERCIAL.ADMI_CARACTERISTICA
WHERE
    DESCRIPCION_CARACTERISTICA = 'ELEMENTO_CLIENTE_ID';
-- REVERSO LA CARACTERISTICA 'INTERFACE_ELEMENTO_CLIENTE_ID'
DELETE DB_COMERCIAL.ADMI_CARACTERISTICA
WHERE
    DESCRIPCION_CARACTERISTICA = 'INTERFACE_ELEMENTO_CLIENTE_ID';
-- REVERSO LA CARACTERISTICA 'ID_PERSONA_EMPRESA_ROL_CARAC_AS_PRIVADO'
DELETE DB_COMERCIAL.ADMI_CARACTERISTICA
WHERE
    DESCRIPCION_CARACTERISTICA = 'ID_PERSONA_EMPRESA_ROL_CARAC_AS_PRIVADO';
-- REVERSO LA CARACTERISTICA 'PRODUCTO_ID'
DELETE DB_COMERCIAL.ADMI_CARACTERISTICA
WHERE
    DESCRIPCION_CARACTERISTICA = 'PRODUCTO_ID';
-- REVERSO LA CARACTERISTICA 'TIPO_RECURSO'
DELETE DB_COMERCIAL.ADMI_CARACTERISTICA
WHERE
    DESCRIPCION_CARACTERISTICA = 'TIPO_RECURSO';

COMMIT;
/
