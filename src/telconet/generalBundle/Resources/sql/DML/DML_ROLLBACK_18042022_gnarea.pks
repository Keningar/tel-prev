DELETE FROM DB_COMERCIAL.ADMI_CARACTERISTICA WHERE descripcion_caracteristica = 'CLONACION_FACTURA';
DELETE FROM DB_COMERCIAL.ADMI_CARACTERISTICA WHERE descripcion_caracteristica = 'NUMERO_FACTURA_PADRE';

DELETE FROM db_general.admi_parametro_cab WHERE nombre_parametro = 'CLONACION DE FACTURAS';

COMMIT;