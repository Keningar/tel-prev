-- REVERSO DE LA CABECERA Y DETALLES DEL PARAMETROS DE 'CONFIG_PRODUCTO_DATOS_SAFE_CITY'
DELETE DB_GENERAL.ADMI_PARAMETRO_DET
WHERE
    PARAMETRO_ID = (
        SELECT
            ID_PARAMETRO
        FROM
            DB_GENERAL.ADMI_PARAMETRO_CAB
        WHERE
            NOMBRE_PARAMETRO = 'CONFIG_PRODUCTO_DATOS_SAFE_CITY'
    );
DELETE DB_GENERAL.ADMI_PARAMETRO_CAB
WHERE
    NOMBRE_PARAMETRO = 'CONFIG_PRODUCTO_DATOS_SAFE_CITY';

-- REVERSO DE LA CABECERA Y DETALLES DEL PARAMETROS DE 'PROD_Cantidad Camaras'
DELETE DB_GENERAL.ADMI_PARAMETRO_DET
WHERE
    PARAMETRO_ID = (
        SELECT
            ID_PARAMETRO
        FROM
            DB_GENERAL.ADMI_PARAMETRO_CAB
        WHERE
            NOMBRE_PARAMETRO = 'PROD_Cantidad Camaras'
    );
DELETE DB_GENERAL.ADMI_PARAMETRO_CAB
WHERE
    NOMBRE_PARAMETRO = 'PROD_Cantidad Camaras';

-- REVERSO DE LA CARACTERISTICA PARA LA VPN GPON
DELETE DB_COMERCIAL.ADMI_CARACTERISTICA
WHERE
    DESCRIPCION_CARACTERISTICA = 'VPN_GPON';

--Eliminamos la relación de la caracteristica MAC ONT.
DELETE FROM db_comercial.admi_producto_caracteristica
WHERE
    producto_id IN (
        SELECT
            id_producto
        FROM
            db_comercial.admi_producto
        WHERE
            descripcion_producto = 'Internet Dedicado'
            AND empresa_cod = 10
            AND nombre_tecnico <> 'FINANCIERO'
            AND estado = 'Activo'
    )
    and caracteristica_id IN (
        SELECT
            id_caracteristica
        FROM
            db_comercial.admi_caracteristica
        WHERE
            descripcion_caracteristica = 'MAC ONT'
    );
DELETE FROM db_comercial.admi_producto_caracteristica
WHERE
    producto_id IN (
        SELECT
            id_producto
        FROM
            db_comercial.admi_producto
        WHERE
            descripcion_producto = 'Internet MPLS'
            AND codigo_producto = 'SISCTN'
            AND empresa_cod = 10
            AND nombre_tecnico <> 'FINANCIERO'
            AND estado = 'Inactivo'
    )
    and caracteristica_id IN (
        SELECT
            id_caracteristica
        FROM
            db_comercial.admi_caracteristica
        WHERE
            descripcion_caracteristica = 'MAC ONT'
    );
DELETE FROM db_comercial.admi_producto_caracteristica
WHERE
    producto_id IN (
        SELECT
            id_producto
        FROM
            db_comercial.admi_producto
        WHERE
            descripcion_producto = 'L3MPLS'
            AND empresa_cod = 10
            AND nombre_tecnico <> 'FINANCIERO'
            AND estado = 'Activo'
    )
    and caracteristica_id IN (
        SELECT
            id_caracteristica
        FROM
            db_comercial.admi_caracteristica
        WHERE
            descripcion_caracteristica = 'MAC ONT'
    );
DELETE FROM db_comercial.admi_producto_caracteristica
WHERE
    producto_id IN (
        SELECT
            id_producto
        FROM
            db_comercial.admi_producto
        WHERE
            descripcion_producto = 'DATOS GPON VIDEO ANALYTICS CAM'
            AND empresa_cod = 10
            AND nombre_tecnico <> 'FINANCIERO'
            AND estado = 'Activo'
    )
    and caracteristica_id IN (
        SELECT
            id_caracteristica
        FROM
            db_comercial.admi_caracteristica
        WHERE
            descripcion_caracteristica = 'MAC ONT'
    );

--Eliminamos la relación de la caracteristica GEM-PORT.
DELETE FROM db_comercial.admi_producto_caracteristica
WHERE
    producto_id IN (
        SELECT
            id_producto
        FROM
            db_comercial.admi_producto
        WHERE
            descripcion_producto = 'Internet Dedicado'
            AND empresa_cod = 10
            AND nombre_tecnico <> 'FINANCIERO'
            AND estado = 'Activo'
    )
    and caracteristica_id IN (
        SELECT
            id_caracteristica
        FROM
            db_comercial.admi_caracteristica
        WHERE
            descripcion_caracteristica = 'GEM-PORT'
    );
DELETE FROM db_comercial.admi_producto_caracteristica
WHERE
    producto_id IN (
        SELECT
            id_producto
        FROM
            db_comercial.admi_producto
        WHERE
            descripcion_producto = 'Internet MPLS'
            AND codigo_producto = 'SISCTN'
            AND empresa_cod = 10
            AND nombre_tecnico <> 'FINANCIERO'
            AND estado = 'Inactivo'
    )
    and caracteristica_id IN (
        SELECT
            id_caracteristica
        FROM
            db_comercial.admi_caracteristica
        WHERE
            descripcion_caracteristica = 'GEM-PORT'
    );
DELETE FROM db_comercial.admi_producto_caracteristica
WHERE
    producto_id IN (
        SELECT
            id_producto
        FROM
            db_comercial.admi_producto
        WHERE
            descripcion_producto = 'L3MPLS'
            AND empresa_cod = 10
            AND nombre_tecnico <> 'FINANCIERO'
            AND estado = 'Activo'
    )
    and caracteristica_id IN (
        SELECT
            id_caracteristica
        FROM
            db_comercial.admi_caracteristica
        WHERE
            descripcion_caracteristica = 'GEM-PORT'
    );
DELETE FROM db_comercial.admi_producto_caracteristica
WHERE
    producto_id IN (
        SELECT
            id_producto
        FROM
            db_comercial.admi_producto
        WHERE
            descripcion_producto = 'DATOS GPON VIDEO ANALYTICS CAM'
            AND empresa_cod = 10
            AND nombre_tecnico <> 'FINANCIERO'
            AND estado = 'Activo'
    )
    and caracteristica_id IN (
        SELECT
            id_caracteristica
        FROM
            db_comercial.admi_caracteristica
        WHERE
            descripcion_caracteristica = 'GEM-PORT'
    );

--Eliminamos la relación de la caracteristica TRAFFIC-TABLE.
DELETE FROM db_comercial.admi_producto_caracteristica
WHERE
    producto_id IN (
        SELECT
            id_producto
        FROM
            db_comercial.admi_producto
        WHERE
            descripcion_producto = 'Internet Dedicado'
            AND empresa_cod = 10
            AND nombre_tecnico <> 'FINANCIERO'
            AND estado = 'Activo'
    )
    and caracteristica_id IN (
        SELECT
            id_caracteristica
        FROM
            db_comercial.admi_caracteristica
        WHERE
            descripcion_caracteristica = 'TRAFFIC-TABLE'
    );
DELETE FROM db_comercial.admi_producto_caracteristica
WHERE
    producto_id IN (
        SELECT
            id_producto
        FROM
            db_comercial.admi_producto
        WHERE
            descripcion_producto = 'Internet MPLS'
            AND codigo_producto = 'SISCTN'
            AND empresa_cod = 10
            AND nombre_tecnico <> 'FINANCIERO'
            AND estado = 'Inactivo'
    )
    and caracteristica_id IN (
        SELECT
            id_caracteristica
        FROM
            db_comercial.admi_caracteristica
        WHERE
            descripcion_caracteristica = 'TRAFFIC-TABLE'
    );
DELETE FROM db_comercial.admi_producto_caracteristica
WHERE
    producto_id IN (
        SELECT
            id_producto
        FROM
            db_comercial.admi_producto
        WHERE
            descripcion_producto = 'L3MPLS'
            AND empresa_cod = 10
            AND nombre_tecnico <> 'FINANCIERO'
            AND estado = 'Activo'
    )
    and caracteristica_id IN (
        SELECT
            id_caracteristica
        FROM
            db_comercial.admi_caracteristica
        WHERE
            descripcion_caracteristica = 'TRAFFIC-TABLE'
    );
DELETE FROM db_comercial.admi_producto_caracteristica
WHERE
    producto_id IN (
        SELECT
            id_producto
        FROM
            db_comercial.admi_producto
        WHERE
            descripcion_producto = 'DATOS GPON VIDEO ANALYTICS CAM'
            AND empresa_cod = 10
            AND nombre_tecnico <> 'FINANCIERO'
            AND estado = 'Activo'
    )
    and caracteristica_id IN (
        SELECT
            id_caracteristica
        FROM
            db_comercial.admi_caracteristica
        WHERE
            descripcion_caracteristica = 'TRAFFIC-TABLE'
    );

--Eliminamos la relación de la caracteristica T-CONT.
DELETE FROM db_comercial.admi_producto_caracteristica
WHERE
    producto_id IN (
        SELECT
            id_producto
        FROM
            db_comercial.admi_producto
        WHERE
            descripcion_producto = 'Internet Dedicado'
            AND empresa_cod = 10
            AND nombre_tecnico <> 'FINANCIERO'
            AND estado = 'Activo'
    )
    and caracteristica_id IN (
        SELECT
            id_caracteristica
        FROM
            db_comercial.admi_caracteristica
        WHERE
            descripcion_caracteristica = 'T-CONT'
    );
DELETE FROM db_comercial.admi_producto_caracteristica
WHERE
    producto_id IN (
        SELECT
            id_producto
        FROM
            db_comercial.admi_producto
        WHERE
            descripcion_producto = 'Internet MPLS'
            AND codigo_producto = 'SISCTN'
            AND empresa_cod = 10
            AND nombre_tecnico <> 'FINANCIERO'
            AND estado = 'Inactivo'
    )
    and caracteristica_id IN (
        SELECT
            id_caracteristica
        FROM
            db_comercial.admi_caracteristica
        WHERE
            descripcion_caracteristica = 'T-CONT'
    );
DELETE FROM db_comercial.admi_producto_caracteristica
WHERE
    producto_id IN (
        SELECT
            id_producto
        FROM
            db_comercial.admi_producto
        WHERE
            descripcion_producto = 'L3MPLS'
            AND empresa_cod = 10
            AND nombre_tecnico <> 'FINANCIERO'
            AND estado = 'Activo'
    )
    and caracteristica_id IN (
        SELECT
            id_caracteristica
        FROM
            db_comercial.admi_caracteristica
        WHERE
            descripcion_caracteristica = 'T-CONT'
    );
DELETE FROM db_comercial.admi_producto_caracteristica
WHERE
    producto_id IN (
        SELECT
            id_producto
        FROM
            db_comercial.admi_producto
        WHERE
            descripcion_producto = 'DATOS GPON VIDEO ANALYTICS CAM'
            AND empresa_cod = 10
            AND nombre_tecnico <> 'FINANCIERO'
            AND estado = 'Activo'
    )
    and caracteristica_id IN (
        SELECT
            id_caracteristica
        FROM
            db_comercial.admi_caracteristica
        WHERE
            descripcion_caracteristica = 'T-CONT'
    );

--Eliminamos la relación de la caracteristica ID-MAPPING.
DELETE FROM db_comercial.admi_producto_caracteristica
WHERE
    producto_id IN (
        SELECT
            id_producto
        FROM
            db_comercial.admi_producto
        WHERE
            descripcion_producto = 'Internet Dedicado'
            AND empresa_cod = 10
            AND nombre_tecnico <> 'FINANCIERO'
            AND estado = 'Activo'
    )
    and caracteristica_id IN (
        SELECT
            id_caracteristica
        FROM
            db_comercial.admi_caracteristica
        WHERE
            descripcion_caracteristica = 'ID-MAPPING'
    );
DELETE FROM db_comercial.admi_producto_caracteristica
WHERE
    producto_id IN (
        SELECT
            id_producto
        FROM
            db_comercial.admi_producto
        WHERE
            descripcion_producto = 'Internet MPLS'
            AND codigo_producto = 'SISCTN'
            AND empresa_cod = 10
            AND nombre_tecnico <> 'FINANCIERO'
            AND estado = 'Inactivo'
    )
    and caracteristica_id IN (
        SELECT
            id_caracteristica
        FROM
            db_comercial.admi_caracteristica
        WHERE
            descripcion_caracteristica = 'ID-MAPPING'
    );
DELETE FROM db_comercial.admi_producto_caracteristica
WHERE
    producto_id IN (
        SELECT
            id_producto
        FROM
            db_comercial.admi_producto
        WHERE
            descripcion_producto = 'L3MPLS'
            AND empresa_cod = 10
            AND nombre_tecnico <> 'FINANCIERO'
            AND estado = 'Activo'
    )
    and caracteristica_id IN (
        SELECT
            id_caracteristica
        FROM
            db_comercial.admi_caracteristica
        WHERE
            descripcion_caracteristica = 'ID-MAPPING'
    );
DELETE FROM db_comercial.admi_producto_caracteristica
WHERE
    producto_id IN (
        SELECT
            id_producto
        FROM
            db_comercial.admi_producto
        WHERE
            descripcion_producto = 'DATOS GPON VIDEO ANALYTICS CAM'
            AND empresa_cod = 10
            AND nombre_tecnico <> 'FINANCIERO'
            AND estado = 'Activo'
    )
    and caracteristica_id IN (
        SELECT
            id_caracteristica
        FROM
            db_comercial.admi_caracteristica
        WHERE
            descripcion_caracteristica = 'ID-MAPPING'
    );

-- REVERSO DE LA CARACTERISTICA PARA LA T-CONT
DELETE DB_COMERCIAL.ADMI_CARACTERISTICA
WHERE
    DESCRIPCION_CARACTERISTICA = 'T-CONT';

-- REVERSO DE LA CARACTERISTICA PARA LA ID-MAPPING
DELETE DB_COMERCIAL.ADMI_CARACTERISTICA
WHERE
    DESCRIPCION_CARACTERISTICA = 'ID-MAPPING';

--Eliminamos la relación de la caracteristica GEM-PORT-MONITOREO.
DELETE FROM db_comercial.admi_producto_caracteristica
WHERE
    producto_id IN (
        SELECT
            id_producto
        FROM
            db_comercial.admi_producto
        WHERE
            descripcion_producto = 'Internet Dedicado'
            AND empresa_cod = 10
            AND nombre_tecnico <> 'FINANCIERO'
            AND estado = 'Activo'
    )
    and caracteristica_id IN (
        SELECT
            id_caracteristica
        FROM
            db_comercial.admi_caracteristica
        WHERE
            descripcion_caracteristica = 'GEM-PORT-MONITOREO'
    );
DELETE FROM db_comercial.admi_producto_caracteristica
WHERE
    producto_id IN (
        SELECT
            id_producto
        FROM
            db_comercial.admi_producto
        WHERE
            descripcion_producto = 'Internet MPLS'
            AND codigo_producto = 'SISCTN'
            AND empresa_cod = 10
            AND nombre_tecnico <> 'FINANCIERO'
            AND estado = 'Inactivo'
    )
    and caracteristica_id IN (
        SELECT
            id_caracteristica
        FROM
            db_comercial.admi_caracteristica
        WHERE
            descripcion_caracteristica = 'GEM-PORT-MONITOREO'
    );
DELETE FROM db_comercial.admi_producto_caracteristica
WHERE
    producto_id IN (
        SELECT
            id_producto
        FROM
            db_comercial.admi_producto
        WHERE
            descripcion_producto = 'L3MPLS'
            AND empresa_cod = 10
            AND nombre_tecnico <> 'FINANCIERO'
            AND estado = 'Activo'
    )
    and caracteristica_id IN (
        SELECT
            id_caracteristica
        FROM
            db_comercial.admi_caracteristica
        WHERE
            descripcion_caracteristica = 'GEM-PORT-MONITOREO'
    );
DELETE FROM db_comercial.admi_producto_caracteristica
WHERE
    producto_id IN (
        SELECT
            id_producto
        FROM
            db_comercial.admi_producto
        WHERE
            descripcion_producto = 'DATOS GPON VIDEO ANALYTICS CAM'
            AND empresa_cod = 10
            AND nombre_tecnico <> 'FINANCIERO'
            AND estado = 'Activo'
    )
    and caracteristica_id IN (
        SELECT
            id_caracteristica
        FROM
            db_comercial.admi_caracteristica
        WHERE
            descripcion_caracteristica = 'GEM-PORT-MONITOREO'
    );

--Eliminamos la relación de la caracteristica TRAFFIC-TABLE-MONITOREO.
DELETE FROM db_comercial.admi_producto_caracteristica
WHERE
    producto_id IN (
        SELECT
            id_producto
        FROM
            db_comercial.admi_producto
        WHERE
            descripcion_producto = 'Internet Dedicado'
            AND empresa_cod = 10
            AND nombre_tecnico <> 'FINANCIERO'
            AND estado = 'Activo'
    )
    and caracteristica_id IN (
        SELECT
            id_caracteristica
        FROM
            db_comercial.admi_caracteristica
        WHERE
            descripcion_caracteristica = 'TRAFFIC-TABLE-MONITOREO'
    );
DELETE FROM db_comercial.admi_producto_caracteristica
WHERE
    producto_id IN (
        SELECT
            id_producto
        FROM
            db_comercial.admi_producto
        WHERE
            descripcion_producto = 'Internet MPLS'
            AND codigo_producto = 'SISCTN'
            AND empresa_cod = 10
            AND nombre_tecnico <> 'FINANCIERO'
            AND estado = 'Inactivo'
    )
    and caracteristica_id IN (
        SELECT
            id_caracteristica
        FROM
            db_comercial.admi_caracteristica
        WHERE
            descripcion_caracteristica = 'TRAFFIC-TABLE-MONITOREO'
    );
DELETE FROM db_comercial.admi_producto_caracteristica
WHERE
    producto_id IN (
        SELECT
            id_producto
        FROM
            db_comercial.admi_producto
        WHERE
            descripcion_producto = 'L3MPLS'
            AND empresa_cod = 10
            AND nombre_tecnico <> 'FINANCIERO'
            AND estado = 'Activo'
    )
    and caracteristica_id IN (
        SELECT
            id_caracteristica
        FROM
            db_comercial.admi_caracteristica
        WHERE
            descripcion_caracteristica = 'TRAFFIC-TABLE-MONITOREO'
    );
DELETE FROM db_comercial.admi_producto_caracteristica
WHERE
    producto_id IN (
        SELECT
            id_producto
        FROM
            db_comercial.admi_producto
        WHERE
            descripcion_producto = 'DATOS GPON VIDEO ANALYTICS CAM'
            AND empresa_cod = 10
            AND nombre_tecnico <> 'FINANCIERO'
            AND estado = 'Activo'
    )
    and caracteristica_id IN (
        SELECT
            id_caracteristica
        FROM
            db_comercial.admi_caracteristica
        WHERE
            descripcion_caracteristica = 'TRAFFIC-TABLE-MONITOREO'
    );

--Eliminamos la relación de la caracteristica T-CONT-MONITOREO.
DELETE FROM db_comercial.admi_producto_caracteristica
WHERE
    producto_id IN (
        SELECT
            id_producto
        FROM
            db_comercial.admi_producto
        WHERE
            descripcion_producto = 'Internet Dedicado'
            AND empresa_cod = 10
            AND nombre_tecnico <> 'FINANCIERO'
            AND estado = 'Activo'
    )
    and caracteristica_id IN (
        SELECT
            id_caracteristica
        FROM
            db_comercial.admi_caracteristica
        WHERE
            descripcion_caracteristica = 'T-CONT-MONITOREO'
    );
DELETE FROM db_comercial.admi_producto_caracteristica
WHERE
    producto_id IN (
        SELECT
            id_producto
        FROM
            db_comercial.admi_producto
        WHERE
            descripcion_producto = 'Internet MPLS'
            AND codigo_producto = 'SISCTN'
            AND empresa_cod = 10
            AND nombre_tecnico <> 'FINANCIERO'
            AND estado = 'Inactivo'
    )
    and caracteristica_id IN (
        SELECT
            id_caracteristica
        FROM
            db_comercial.admi_caracteristica
        WHERE
            descripcion_caracteristica = 'T-CONT-MONITOREO'
    );
DELETE FROM db_comercial.admi_producto_caracteristica
WHERE
    producto_id IN (
        SELECT
            id_producto
        FROM
            db_comercial.admi_producto
        WHERE
            descripcion_producto = 'L3MPLS'
            AND empresa_cod = 10
            AND nombre_tecnico <> 'FINANCIERO'
            AND estado = 'Activo'
    )
    and caracteristica_id IN (
        SELECT
            id_caracteristica
        FROM
            db_comercial.admi_caracteristica
        WHERE
            descripcion_caracteristica = 'T-CONT-MONITOREO'
    );
DELETE FROM db_comercial.admi_producto_caracteristica
WHERE
    producto_id IN (
        SELECT
            id_producto
        FROM
            db_comercial.admi_producto
        WHERE
            descripcion_producto = 'DATOS GPON VIDEO ANALYTICS CAM'
            AND empresa_cod = 10
            AND nombre_tecnico <> 'FINANCIERO'
            AND estado = 'Activo'
    )
    and caracteristica_id IN (
        SELECT
            id_caracteristica
        FROM
            db_comercial.admi_caracteristica
        WHERE
            descripcion_caracteristica = 'T-CONT-MONITOREO'
    );

--Eliminamos la relación de la caracteristica ID-MAPPING-MONITOREO.
DELETE FROM db_comercial.admi_producto_caracteristica
WHERE
    producto_id IN (
        SELECT
            id_producto
        FROM
            db_comercial.admi_producto
        WHERE
            descripcion_producto = 'Internet Dedicado'
            AND empresa_cod = 10
            AND nombre_tecnico <> 'FINANCIERO'
            AND estado = 'Activo'
    )
    and caracteristica_id IN (
        SELECT
            id_caracteristica
        FROM
            db_comercial.admi_caracteristica
        WHERE
            descripcion_caracteristica = 'ID-MAPPING-MONITOREO'
    );
DELETE FROM db_comercial.admi_producto_caracteristica
WHERE
    producto_id IN (
        SELECT
            id_producto
        FROM
            db_comercial.admi_producto
        WHERE
            descripcion_producto = 'Internet MPLS'
            AND codigo_producto = 'SISCTN'
            AND empresa_cod = 10
            AND nombre_tecnico <> 'FINANCIERO'
            AND estado = 'Inactivo'
    )
    and caracteristica_id IN (
        SELECT
            id_caracteristica
        FROM
            db_comercial.admi_caracteristica
        WHERE
            descripcion_caracteristica = 'ID-MAPPING-MONITOREO'
    );
DELETE FROM db_comercial.admi_producto_caracteristica
WHERE
    producto_id IN (
        SELECT
            id_producto
        FROM
            db_comercial.admi_producto
        WHERE
            descripcion_producto = 'L3MPLS'
            AND empresa_cod = 10
            AND nombre_tecnico <> 'FINANCIERO'
            AND estado = 'Activo'
    )
    and caracteristica_id IN (
        SELECT
            id_caracteristica
        FROM
            db_comercial.admi_caracteristica
        WHERE
            descripcion_caracteristica = 'ID-MAPPING-MONITOREO'
    );
DELETE FROM db_comercial.admi_producto_caracteristica
WHERE
    producto_id IN (
        SELECT
            id_producto
        FROM
            db_comercial.admi_producto
        WHERE
            descripcion_producto = 'DATOS GPON VIDEO ANALYTICS CAM'
            AND empresa_cod = 10
            AND nombre_tecnico <> 'FINANCIERO'
            AND estado = 'Activo'
    )
    and caracteristica_id IN (
        SELECT
            id_caracteristica
        FROM
            db_comercial.admi_caracteristica
        WHERE
            descripcion_caracteristica = 'ID-MAPPING-MONITOREO'
    );

--Eliminamos la relación de la caracteristica VLAN-MONITOREO.
DELETE FROM db_comercial.admi_producto_caracteristica
WHERE
    producto_id IN (
        SELECT
            id_producto
        FROM
            db_comercial.admi_producto
        WHERE
            descripcion_producto = 'Internet Dedicado'
            AND empresa_cod = 10
            AND nombre_tecnico <> 'FINANCIERO'
            AND estado = 'Activo'
    )
    and caracteristica_id IN (
        SELECT
            id_caracteristica
        FROM
            db_comercial.admi_caracteristica
        WHERE
            descripcion_caracteristica = 'VLAN-MONITOREO'
    );
DELETE FROM db_comercial.admi_producto_caracteristica
WHERE
    producto_id IN (
        SELECT
            id_producto
        FROM
            db_comercial.admi_producto
        WHERE
            descripcion_producto = 'Internet MPLS'
            AND codigo_producto = 'SISCTN'
            AND empresa_cod = 10
            AND nombre_tecnico <> 'FINANCIERO'
            AND estado = 'Inactivo'
    )
    and caracteristica_id IN (
        SELECT
            id_caracteristica
        FROM
            db_comercial.admi_caracteristica
        WHERE
            descripcion_caracteristica = 'VLAN-MONITOREO'
    );
DELETE FROM db_comercial.admi_producto_caracteristica
WHERE
    producto_id IN (
        SELECT
            id_producto
        FROM
            db_comercial.admi_producto
        WHERE
            descripcion_producto = 'L3MPLS'
            AND empresa_cod = 10
            AND nombre_tecnico <> 'FINANCIERO'
            AND estado = 'Activo'
    )
    and caracteristica_id IN (
        SELECT
            id_caracteristica
        FROM
            db_comercial.admi_caracteristica
        WHERE
            descripcion_caracteristica = 'VLAN-MONITOREO'
    );
DELETE FROM db_comercial.admi_producto_caracteristica
WHERE
    producto_id IN (
        SELECT
            id_producto
        FROM
            db_comercial.admi_producto
        WHERE
            descripcion_producto = 'DATOS GPON VIDEO ANALYTICS CAM'
            AND empresa_cod = 10
            AND nombre_tecnico <> 'FINANCIERO'
            AND estado = 'Activo'
    )
    and caracteristica_id IN (
        SELECT
            id_caracteristica
        FROM
            db_comercial.admi_caracteristica
        WHERE
            descripcion_caracteristica = 'VLAN-MONITOREO'
    );

-- REVERSO DE LA CARACTERISTICA PARA LA GEM-PORT-MONITOREO
DELETE DB_COMERCIAL.ADMI_CARACTERISTICA
WHERE
    DESCRIPCION_CARACTERISTICA = 'GEM-PORT-MONITOREO';

-- REVERSO DE LA CARACTERISTICA PARA LA TRAFFIC-TABLE-MONITOREO
DELETE DB_COMERCIAL.ADMI_CARACTERISTICA
WHERE
    DESCRIPCION_CARACTERISTICA = 'TRAFFIC-TABLE-MONITOREO';

-- REVERSO DE LA CARACTERISTICA PARA LA T-CONT-MONITOREO
DELETE DB_COMERCIAL.ADMI_CARACTERISTICA
WHERE
    DESCRIPCION_CARACTERISTICA = 'T-CONT-MONITOREO';

-- REVERSO DE LA CARACTERISTICA PARA LA ID-MAPPING-MONITOREO
DELETE DB_COMERCIAL.ADMI_CARACTERISTICA
WHERE
    DESCRIPCION_CARACTERISTICA = 'ID-MAPPING-MONITOREO';

-- REVERSO DE LA CARACTERISTICA PARA LA VLAN-MONITOREO
DELETE DB_COMERCIAL.ADMI_CARACTERISTICA
WHERE
    DESCRIPCION_CARACTERISTICA = 'VLAN-MONITOREO';

-- REVERSO DE LA CARACTERISTICA PARA EL ID_DETALLE_TAREA_INSTALACION
DELETE DB_COMERCIAL.ADMI_CARACTERISTICA
WHERE
    DESCRIPCION_CARACTERISTICA = 'ID_DETALLE_TAREA_INSTALACION';

--Eliminamos la relación de la caracteristica LINE-PROFILE-NAME.
DELETE FROM db_comercial.admi_producto_caracteristica
WHERE
    producto_id IN (
        SELECT
            id_producto
        FROM
            db_comercial.admi_producto
        WHERE
            descripcion_producto = 'Internet Dedicado'
            AND empresa_cod = 10
            AND nombre_tecnico <> 'FINANCIERO'
            AND estado = 'Activo'
    )
    and caracteristica_id IN (
        SELECT
            id_caracteristica
        FROM
            db_comercial.admi_caracteristica
        WHERE
            descripcion_caracteristica = 'LINE-PROFILE-NAME'
    );
DELETE FROM db_comercial.admi_producto_caracteristica
WHERE
    producto_id IN (
        SELECT
            id_producto
        FROM
            db_comercial.admi_producto
        WHERE
            descripcion_producto = 'Internet MPLS'
            AND codigo_producto = 'SISCTN'
            AND empresa_cod = 10
            AND nombre_tecnico <> 'FINANCIERO'
            AND estado = 'Inactivo'
    )
    and caracteristica_id IN (
        SELECT
            id_caracteristica
        FROM
            db_comercial.admi_caracteristica
        WHERE
            descripcion_caracteristica = 'LINE-PROFILE-NAME'
    );
DELETE FROM db_comercial.admi_producto_caracteristica
WHERE
    producto_id IN (
        SELECT
            id_producto
        FROM
            db_comercial.admi_producto
        WHERE
            descripcion_producto = 'L3MPLS'
            AND empresa_cod = 10
            AND nombre_tecnico <> 'FINANCIERO'
            AND estado = 'Activo'
    )
    and caracteristica_id IN (
        SELECT
            id_caracteristica
        FROM
            db_comercial.admi_caracteristica
        WHERE
            descripcion_caracteristica = 'LINE-PROFILE-NAME'
    );
DELETE FROM db_comercial.admi_producto_caracteristica
WHERE
    producto_id IN (
        SELECT
            id_producto
        FROM
            db_comercial.admi_producto
        WHERE
            descripcion_producto = 'DATOS GPON VIDEO ANALYTICS CAM'
            AND empresa_cod = 10
            AND nombre_tecnico <> 'FINANCIERO'
            AND estado = 'Activo'
    )
    and caracteristica_id IN (
        SELECT
            id_caracteristica
        FROM
            db_comercial.admi_caracteristica
        WHERE
            descripcion_caracteristica = 'LINE-PROFILE-NAME'
    );

-- REVERSO DE LA CABECERA Y DETALLES DEL PARAMETROS DE 'PROTOCOLOS_ENRUTAMIENTO_GPON'
DELETE DB_GENERAL.ADMI_PARAMETRO_DET
WHERE
    PARAMETRO_ID = (
        SELECT
            ID_PARAMETRO
        FROM
            DB_GENERAL.ADMI_PARAMETRO_CAB
        WHERE
            NOMBRE_PARAMETRO = 'PROTOCOLOS_ENRUTAMIENTO_GPON'
    );
DELETE DB_GENERAL.ADMI_PARAMETRO_CAB
WHERE
    NOMBRE_PARAMETRO = 'PROTOCOLOS_ENRUTAMIENTO_GPON';

-- REVERSO DE LA CABECERA Y DETALLES DEL PARAMETROS DE 'PARAMETROS MASCARA Y PREFIJOS DE SUBREDES PRODUCTO'
DELETE DB_GENERAL.ADMI_PARAMETRO_DET
WHERE
    PARAMETRO_ID = (
        SELECT
            ID_PARAMETRO
        FROM
            DB_GENERAL.ADMI_PARAMETRO_CAB
        WHERE
            NOMBRE_PARAMETRO = 'PARAMETROS MASCARA Y PREFIJOS DE SUBREDES PRODUCTO'
    );
DELETE DB_GENERAL.ADMI_PARAMETRO_CAB
WHERE
    NOMBRE_PARAMETRO = 'PARAMETROS MASCARA Y PREFIJOS DE SUBREDES PRODUCTO'

-- REVERSO DE LA CABECERA Y DETALLES DEL PARAMETROS DE 'PROYECTO PARAMETRIZAR FILTRO DE CAJAS'
DELETE DB_GENERAL.ADMI_PARAMETRO_DET
WHERE
    PARAMETRO_ID = (
        SELECT
            ID_PARAMETRO
        FROM
            DB_GENERAL.ADMI_PARAMETRO_CAB
        WHERE
            NOMBRE_PARAMETRO = 'PROYECTO PARAMETRIZAR FILTRO DE CAJAS'
    );
DELETE DB_GENERAL.ADMI_PARAMETRO_CAB
WHERE
    NOMBRE_PARAMETRO = 'PROYECTO PARAMETRIZAR FILTRO DE CAJAS';

-- REVERSO DE LA CABECERA Y DETALLES DEL PARAMETROS DE 'PROD_VELOCIDAD_GPON'
DELETE DB_GENERAL.ADMI_PARAMETRO_DET
WHERE
    PARAMETRO_ID = (
        SELECT
            ID_PARAMETRO
        FROM
            DB_GENERAL.ADMI_PARAMETRO_CAB
        WHERE
            NOMBRE_PARAMETRO = 'PROD_VELOCIDAD_GPON'
    );
DELETE DB_GENERAL.ADMI_PARAMETRO_CAB
WHERE
    NOMBRE_PARAMETRO = 'PROD_VELOCIDAD_GPON';

--Eliminamos la relación de la caracteristica VELOCIDAD_GPON.
DELETE FROM db_comercial.admi_producto_caracteristica
WHERE
    producto_id IN (
        SELECT
            id_producto
        FROM
            db_comercial.admi_producto
        WHERE
            descripcion_producto = 'Internet Dedicado'
            AND empresa_cod = 10
            AND nombre_tecnico <> 'FINANCIERO'
            AND estado = 'Activo'
    )
    and caracteristica_id IN (
        SELECT
            id_caracteristica
        FROM
            db_comercial.admi_caracteristica
        WHERE
            descripcion_caracteristica = 'VELOCIDAD_GPON'
    );
DELETE FROM db_comercial.admi_producto_caracteristica
WHERE
    producto_id IN (
        SELECT
            id_producto
        FROM
            db_comercial.admi_producto
        WHERE
            descripcion_producto = 'Internet MPLS'
            AND codigo_producto = 'SISCTN'
            AND empresa_cod = 10
            AND nombre_tecnico <> 'FINANCIERO'
            AND estado = 'Inactivo'
    )
    and caracteristica_id IN (
        SELECT
            id_caracteristica
        FROM
            db_comercial.admi_caracteristica
        WHERE
            descripcion_caracteristica = 'VELOCIDAD_GPON'
    );
DELETE FROM db_comercial.admi_producto_caracteristica
WHERE
    producto_id IN (
        SELECT
            id_producto
        FROM
            db_comercial.admi_producto
        WHERE
            descripcion_producto = 'L3MPLS'
            AND empresa_cod = 10
            AND nombre_tecnico <> 'FINANCIERO'
            AND estado = 'Activo'
    )
    and caracteristica_id IN (
        SELECT
            id_caracteristica
        FROM
            db_comercial.admi_caracteristica
        WHERE
            descripcion_caracteristica = 'VELOCIDAD_GPON'
    );

COMMIT;
/
