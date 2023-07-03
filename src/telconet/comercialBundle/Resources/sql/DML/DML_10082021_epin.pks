--Inserto el producto disponible para el tm-COMERCIAL
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
VALUES (DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL, (SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB where NOMBRE_PARAMETRO = 'PRODUCTOS_TM_COMERCIAL'),
        'El canal del futbol', 'ECDF', 'prodCanalFutbol', 'El canal del futbol',
        (SELECT ID_PRODUCTO 
                                      FROM DB_COMERCIAL.ADMI_PRODUCTO
                                      WHERE NOMBRE_TECNICO = 'ECDF'), 'Activo', 'epin', sysdate, '127.0.0.1', null, null, null, 'ECDF', '18', null, null, null);

--Inserto las caracteristicas del producto para tm-comercial
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
VALUES (DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL, 
(SELECT ID_PARAMETRO 
FROM DB_GENERAL.ADMI_PARAMETRO_CAB 
where NOMBRE_PARAMETRO = 'CARACTERISTICAS_PROD_ADICIONALES_TM_COMERCIAL'),
        'CORREO ELECTRONICO', 'ECDF', 
        (select ID_PRODUCTO_CARACTERISITICA 
        from DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA 
        where producto_id = (select id_producto 
                            from DB_COMERCIAL.ADMI_PRODUCTO 
                            WHERE DB_COMERCIAL.ADMI_PRODUCTO.NOMBRE_TECNICO = 'ECDF')
          AND CARACTERISTICA_ID = (SELECT ID_CARACTERISTICA 
                                   FROM DB_COMERCIAL.ADMI_CARACTERISTICA 
                                   WHERE DESCRIPCION_CARACTERISTICA = 'CORREO ELECTRONICO')), 'SI', 'SI', 'Activo', 'epin', sysdate, '127.0.0.1', null, null, null, null, '18', null, 'EMAIL', null);


COMMIT;

/                    