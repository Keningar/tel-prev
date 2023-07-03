--=======================================================================
-- Ingreso de la característica del id del elemento del cliente para las características del detalle de solicitud
-- Ingreso de la característica del id de la interface del elemento del cliente para las características del detalle de solicitud
-- Ingreso de la característica del id de la persona empresa rol para las características del detalle de solicitud
-- Ingreso de la característica del id del producto del servicio para las características del detalle de solicitud
-- Ingreso de la característica del tipo de recurso en las características del detalle de solicitud
--=======================================================================

-- INGRESO LA CARACTERISTICA 'ELEMENTO_CLIENTE_ID'
INSERT INTO DB_COMERCIAL.ADMI_CARACTERISTICA 
( 
        ID_CARACTERISTICA, 
        DESCRIPCION_CARACTERISTICA, 
        TIPO_INGRESO, 
        TIPO,
        ESTADO, 
        USR_CREACION, 
        FE_CREACION
)
VALUES
( 
        DB_COMERCIAL.SEQ_ADMI_CARACTERISTICA.NEXTVAL, 
        'ELEMENTO_CLIENTE_ID', 
        'N', 
        'TECNICA', 
        'Activo', 
        'facaicedo', 
        SYSDATE
);
-- INGRESO LA CARACTERISTICA 'INTERFACE_ELEMENTO_CLIENTE_ID'
INSERT INTO DB_COMERCIAL.ADMI_CARACTERISTICA 
( 
        ID_CARACTERISTICA, 
        DESCRIPCION_CARACTERISTICA, 
        TIPO_INGRESO, 
        TIPO,
        ESTADO, 
        USR_CREACION, 
        FE_CREACION
)
VALUES
( 
        DB_COMERCIAL.SEQ_ADMI_CARACTERISTICA.NEXTVAL, 
        'INTERFACE_ELEMENTO_CLIENTE_ID', 
        'N', 
        'TECNICA', 
        'Activo', 
        'facaicedo', 
        SYSDATE
);
-- INGRESO LA CARACTERISTICA 'ID_PERSONA_EMPRESA_ROL_CARAC_AS_PRIVADO'
INSERT INTO DB_COMERCIAL.ADMI_CARACTERISTICA 
( 
        ID_CARACTERISTICA, 
        DESCRIPCION_CARACTERISTICA, 
        TIPO_INGRESO, 
        TIPO,
        ESTADO, 
        USR_CREACION, 
        FE_CREACION
)
VALUES
( 
        DB_COMERCIAL.SEQ_ADMI_CARACTERISTICA.NEXTVAL, 
        'ID_PERSONA_EMPRESA_ROL_CARAC_AS_PRIVADO', 
        'N', 
        'TECNICA', 
        'Activo', 
        'facaicedo', 
        SYSDATE
);
-- INGRESO LA CARACTERISTICA 'PRODUCTO_ID'
INSERT INTO DB_COMERCIAL.ADMI_CARACTERISTICA 
( 
        ID_CARACTERISTICA, 
        DESCRIPCION_CARACTERISTICA, 
        TIPO_INGRESO, 
        TIPO,
        ESTADO, 
        USR_CREACION, 
        FE_CREACION
)
VALUES
( 
        DB_COMERCIAL.SEQ_ADMI_CARACTERISTICA.NEXTVAL, 
        'PRODUCTO_ID', 
        'N', 
        'TECNICA', 
        'Activo', 
        'facaicedo', 
        SYSDATE
);
-- INGRESO LA CARACTERISTICA 'TIPO_RECURSO'
INSERT INTO DB_COMERCIAL.ADMI_CARACTERISTICA 
( 
        ID_CARACTERISTICA, 
        DESCRIPCION_CARACTERISTICA, 
        TIPO_INGRESO, 
        TIPO,
        ESTADO, 
        USR_CREACION, 
        FE_CREACION
)
VALUES
( 
        DB_COMERCIAL.SEQ_ADMI_CARACTERISTICA.NEXTVAL, 
        'TIPO_RECURSO', 
        'T', 
        'TECNICA', 
        'Activo', 
        'facaicedo', 
        SYSDATE
);
COMMIT;
/
