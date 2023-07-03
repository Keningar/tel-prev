-- Eliminar el nuevo tipo de solicitud para cableado ethernet
DELETE FROM db_comercial.admi_tipo_solicitud
WHERE DESCRIPCION_SOLICITUD = 'SOLICITUD DE INSTALACION CABLEADO ETHERNET';

COMMIT;
/