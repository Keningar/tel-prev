--Reverso del detalle de la marca ZTE modelo C650, Tecnologias Permitidas para GPON
DELETE DB_GENERAL.ADMI_PARAMETRO_DET
WHERE PARAMETRO_ID = (
        SELECT id_parametro
        FROM db_general.admi_parametro_cab
        WHERE nombre_parametro = 'NUEVA_RED_GPON_TN'
    )
AND VALOR1 = 'MARCA_TECNOLOGIA_PERMITIDA_GPON'
AND VALOR2 = 'ZTE' AND VALOR3 = 'C650';

--Reverso del modelo ont zte
DELETE DB_GENERAL.ADMI_PARAMETRO_DET
WHERE PARAMETRO_ID = (
        SELECT id_parametro
        FROM db_general.admi_parametro_cab
        WHERE nombre_parametro = 'NUEVA_RED_GPON_TN'
    )
AND VALOR1 = 'MODELO_ONT_ZTE';

-- Reverso del producto caracteristica t-cont-admin para olt zte
DELETE DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA
WHERE
    CARACTERISTICA_ID = (
        SELECT ID_CARACTERISTICA FROM DB_COMERCIAL.ADMI_CARACTERISTICA
        WHERE DESCRIPCION_CARACTERISTICA = 'T-CONT-ADMIN'
    );

--Reverso de la caracteristica t-cont-admin para olt zte
DELETE DB_COMERCIAL.ADMI_CARACTERISTICA
WHERE
    DESCRIPCION_CARACTERISTICA = 'T-CONT-ADMIN';

-- Reverso del producto caracteristica GEM-PORT-ADMIN para olt zte
DELETE DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA
WHERE
    CARACTERISTICA_ID = (
        SELECT ID_CARACTERISTICA FROM DB_COMERCIAL.ADMI_CARACTERISTICA
        WHERE DESCRIPCION_CARACTERISTICA = 'GEM-PORT-ADMIN'
    );

--Reverso de la caracteristica GEM-PORT-ADMIN para olt zte
DELETE DB_COMERCIAL.ADMI_CARACTERISTICA
WHERE
    DESCRIPCION_CARACTERISTICA = 'GEM-PORT-ADMIN';

-- Reverso del producto caracteristica TRAFFIC-TABLE-ADMIN para olt zte
DELETE DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA
WHERE
    CARACTERISTICA_ID = (
        SELECT ID_CARACTERISTICA FROM DB_COMERCIAL.ADMI_CARACTERISTICA
        WHERE DESCRIPCION_CARACTERISTICA = 'TRAFFIC-TABLE-ADMIN'
    );

--Reverso de la caracteristica t-cont-admin para olt zte
DELETE DB_COMERCIAL.ADMI_CARACTERISTICA
WHERE
    DESCRIPCION_CARACTERISTICA = 'TRAFFIC-TABLE-ADMIN';

-- Reverso nuevas capacidades traffic table
UPDATE DB_GENERAL.ADMI_PARAMETRO_DET 
SET VALOR3 = NULL
WHERE PARAMETRO_ID  = (SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB
                        WHERE NOMBRE_PARAMETRO   = 'MAPEO_VELOCIDAD_TRAFFIC_TABLE_GPON' AND ESTADO = 'Activo') 
AND DESCRIPCION = 'MAPEO_VELOCIDAD_TRAFFIC_TABLE_GPON'
AND ESTADO = 'Activo';
COMMIT;
/