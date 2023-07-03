/**
 * Documentación para crear características y modificar características para 
 * producto CLEAR CHANNEL PUNTO A PUNTO
 *
 * @author Josue Valencia <ajvalencia@telconet.ec>
 * @version 1.0 30-12-2022
 */

--INSERT ADMI_CARACTERISTICA
INSERT INTO DB_COMERCIAL.ADMI_CARACTERISTICA VALUES
                                  ( DB_COMERCIAL.SEQ_ADMI_CARACTERISTICA.NEXTVAL,
                                   'ANCHO BANDA',
                                   'S',
                                   'Activo',
                                   SYSDATE,
                                   'ajvalencia',
                                   NULL,
                                   NULL,
                                   'COMERCIAL',
                                   NULL);  
                                   
INSERT INTO DB_COMERCIAL.ADMI_CARACTERISTICA VALUES
                                  ( DB_COMERCIAL.SEQ_ADMI_CARACTERISTICA.NEXTVAL,
                                   'REQUIERE TRANSPORTE',
                                   'S',
                                   'Activo',
                                   SYSDATE,
                                   'ajvalencia',
                                   NULL,
                                   NULL,
                                   'COMERCIAL',
                                   NULL);
                                   
INSERT INTO DB_COMERCIAL.ADMI_CARACTERISTICA VALUES
                                  ( DB_COMERCIAL.SEQ_ADMI_CARACTERISTICA.NEXTVAL,
                                   'MODELO BACKUP',
                                   'S',
                                   'Activo',
                                   SYSDATE,
                                   'ajvalencia',
                                   NULL,
                                   NULL,
                                   'COMERCIAL',
                                   NULL);
                                   
                                   
INSERT INTO DB_COMERCIAL.ADMI_CARACTERISTICA VALUES
                                  ( DB_COMERCIAL.SEQ_ADMI_CARACTERISTICA.NEXTVAL,
                                   'INTERFACE_EQUIPO',
                                   'N',
                                   'Activo',
                                   SYSDATE,
                                   'ajvalencia',
                                   NULL,
                                   NULL,
                                   'COMERCIAL',
                                   NULL);  

INSERT INTO DB_COMERCIAL.ADMI_CARACTERISTICA VALUES
                                  ( DB_COMERCIAL.SEQ_ADMI_CARACTERISTICA.NEXTVAL,
                                   'SERIE_EQUIPO',
                                   'N',
                                   'Activo',
                                   SYSDATE,
                                   'ajvalencia',
                                   NULL,
                                   NULL,
                                   'COMERCIAL',
                                     NULL); 
INSERT INTO DB_COMERCIAL.ADMI_CARACTERISTICA VALUES
                                  ( DB_COMERCIAL.SEQ_ADMI_CARACTERISTICA.NEXTVAL,
                                   'MODELO_EQUIPO',
                                   'N',
                                   'Activo',
                                   SYSDATE,
                                   'ajvalencia',
                                   NULL,
                                   NULL,
                                   'COMERCIAL',
                                     NULL); 

INSERT INTO DB_COMERCIAL.ADMI_CARACTERISTICA VALUES
                                  ( DB_COMERCIAL.SEQ_ADMI_CARACTERISTICA.NEXTVAL,
                                   'IP_EQUIPO',
                                   'N',
                                   'Activo',
                                   SYSDATE,
                                   'ajvalencia',
                                   NULL,
                                   NULL,
                                   'COMERCIAL',
                                     NULL); 

INSERT INTO DB_COMERCIAL.ADMI_CARACTERISTICA VALUES
                                  ( DB_COMERCIAL.SEQ_ADMI_CARACTERISTICA.NEXTVAL,
                                   'DESCRIPCION_EQUIPO',
                                   'N',
                                   'Activo',
                                   SYSDATE,
                                   'ajvalencia',
                                   NULL,
                                   NULL,
                                   'COMERCIAL',
                                     NULL);
                                                                    
--INSERT ADMI_PRODUCTO_CARACTERISTICA
                                                                      
INSERT INTO DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA
    (
    ID_PRODUCTO_CARACTERISITICA,
    PRODUCTO_ID,
    CARACTERISTICA_ID,
    FE_CREACION,
    FE_ULT_MOD,
    USR_CREACION,
    USR_ULT_MOD,
    ESTADO,
    VISIBLE_COMERCIAL
    )
VALUES
    (
        DB_COMERCIAL.SEQ_ADMI_PRODUCTO_CARAC.NEXTVAL,
        (
        SELECT
            ID_PRODUCTO
        FROM
            DB_COMERCIAL.ADMI_PRODUCTO
        WHERE
            DESCRIPCION_PRODUCTO = 'CLEAR CHANNEL PUNTO A PUNTO' AND ESTADO = 'Activo'
    ),
        (
        SELECT
            ID_CARACTERISTICA
        FROM
            DB_COMERCIAL.ADMI_CARACTERISTICA
        WHERE
            DESCRIPCION_CARACTERISTICA = 'ANCHO BANDA' AND ESTADO = 'Activo'
    ),
        SYSDATE,
        NULL,
        'ajvalencia',
        NULL,
        'Activo',
        'SI'
);

INSERT INTO DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA
    (
    ID_PRODUCTO_CARACTERISITICA,
    PRODUCTO_ID,
    CARACTERISTICA_ID,
    FE_CREACION,
    FE_ULT_MOD,
    USR_CREACION,
    USR_ULT_MOD,
    ESTADO,
    VISIBLE_COMERCIAL
    )
VALUES
    (
        DB_COMERCIAL.SEQ_ADMI_PRODUCTO_CARAC.NEXTVAL,
        (
        SELECT
            ID_PRODUCTO
        FROM
            DB_COMERCIAL.ADMI_PRODUCTO
        WHERE
            DESCRIPCION_PRODUCTO = 'CLEAR CHANNEL PUNTO A PUNTO' AND ESTADO = 'Activo'
    ),
        (
        SELECT
            ID_CARACTERISTICA
        FROM
            DB_COMERCIAL.ADMI_CARACTERISTICA
        WHERE
            DESCRIPCION_CARACTERISTICA = 'REQUIERE TRANSPORTE' AND ESTADO = 'Activo'
    ),
        SYSDATE,
        NULL,
        'ajvalencia',
        NULL,
        'Activo',
        'SI'
);

INSERT INTO DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA
    (
    ID_PRODUCTO_CARACTERISITICA,
    PRODUCTO_ID,
    CARACTERISTICA_ID,
    FE_CREACION,
    FE_ULT_MOD,
    USR_CREACION,
    USR_ULT_MOD,
    ESTADO,
    VISIBLE_COMERCIAL
    )
VALUES
    (
        DB_COMERCIAL.SEQ_ADMI_PRODUCTO_CARAC.NEXTVAL,
        (
        SELECT
            ID_PRODUCTO
        FROM
            DB_COMERCIAL.ADMI_PRODUCTO
        WHERE
            DESCRIPCION_PRODUCTO = 'CLEAR CHANNEL PUNTO A PUNTO' AND ESTADO = 'Activo'
    ),
        (
        SELECT
            ID_CARACTERISTICA
        FROM
            DB_COMERCIAL.ADMI_CARACTERISTICA
        WHERE
            DESCRIPCION_CARACTERISTICA = 'VLAN_WAN' AND ESTADO = 'Activo'
    ),
        SYSDATE,
        NULL,
        'ajvalencia',
        NULL,
        'Activo',
        'NO'
);


INSERT INTO DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA
    (
    ID_PRODUCTO_CARACTERISITICA,
    PRODUCTO_ID,
    CARACTERISTICA_ID,
    FE_CREACION,
    FE_ULT_MOD,
    USR_CREACION,
    USR_ULT_MOD,
    ESTADO,
    VISIBLE_COMERCIAL
    )
VALUES
    (
        DB_COMERCIAL.SEQ_ADMI_PRODUCTO_CARAC.NEXTVAL,
        (
        SELECT
            ID_PRODUCTO
        FROM
            DB_COMERCIAL.ADMI_PRODUCTO
        WHERE
            DESCRIPCION_PRODUCTO = 'CLEAR CHANNEL PUNTO A PUNTO' AND ESTADO = 'Activo'
    ),
        (
        SELECT
            ID_CARACTERISTICA
        FROM
            DB_COMERCIAL.ADMI_CARACTERISTICA
        WHERE
            DESCRIPCION_CARACTERISTICA = 'VRF' AND ESTADO = 'Activo'
    ),
        SYSDATE,
        NULL,
        'ajvalencia',
        NULL,
        'Activo',
        'NO'
);

INSERT INTO DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA
    (
    ID_PRODUCTO_CARACTERISITICA,
    PRODUCTO_ID,
    CARACTERISTICA_ID,
    FE_CREACION,
    FE_ULT_MOD,
    USR_CREACION,
    USR_ULT_MOD,
    ESTADO,
    VISIBLE_COMERCIAL
    )
VALUES
    (
        DB_COMERCIAL.SEQ_ADMI_PRODUCTO_CARAC.NEXTVAL,
        (
        SELECT
            ID_PRODUCTO
        FROM
            DB_COMERCIAL.ADMI_PRODUCTO
        WHERE
            DESCRIPCION_PRODUCTO = 'CLEAR CHANNEL PUNTO A PUNTO' AND ESTADO = 'Activo'
    ),
        (
        SELECT
            ID_CARACTERISTICA
        FROM
            DB_COMERCIAL.ADMI_CARACTERISTICA
        WHERE
            DESCRIPCION_CARACTERISTICA = 'NOMBRE PE' AND ESTADO = 'Activo'
    ),
        SYSDATE,
        NULL,
        'ajvalencia',
        NULL,
        'Activo',
        'NO'
);


INSERT INTO DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA
    (
    ID_PRODUCTO_CARACTERISITICA,
    PRODUCTO_ID,
    CARACTERISTICA_ID,
    FE_CREACION,
    FE_ULT_MOD,
    USR_CREACION,
    USR_ULT_MOD,
    ESTADO,
    VISIBLE_COMERCIAL
    )
VALUES
    (
        DB_COMERCIAL.SEQ_ADMI_PRODUCTO_CARAC.NEXTVAL,
        (
        SELECT
            ID_PRODUCTO
        FROM
            DB_COMERCIAL.ADMI_PRODUCTO
        WHERE
            DESCRIPCION_PRODUCTO = 'CLEAR CHANNEL PUNTO A PUNTO' AND ESTADO = 'Activo'
    ),
        (
        SELECT
            ID_CARACTERISTICA
        FROM
            DB_COMERCIAL.ADMI_CARACTERISTICA
        WHERE
            DESCRIPCION_CARACTERISTICA = 'MASCARA WAN' AND ESTADO = 'Activo'
    ),
        SYSDATE,
        NULL,
        'ajvalencia',
        NULL,
        'Activo',
        'NO'
);

INSERT INTO DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA
    (
    ID_PRODUCTO_CARACTERISITICA,
    PRODUCTO_ID,
    CARACTERISTICA_ID,
    FE_CREACION,
    FE_ULT_MOD,
    USR_CREACION,
    USR_ULT_MOD,
    ESTADO,
    VISIBLE_COMERCIAL
    )
VALUES
    (
        DB_COMERCIAL.SEQ_ADMI_PRODUCTO_CARAC.NEXTVAL,
        (
        SELECT
            ID_PRODUCTO
        FROM
            DB_COMERCIAL.ADMI_PRODUCTO
        WHERE
            DESCRIPCION_PRODUCTO = 'CLEAR CHANNEL PUNTO A PUNTO' AND ESTADO = 'Activo'
    ),
        (
        SELECT
            ID_CARACTERISTICA
        FROM
            DB_COMERCIAL.ADMI_CARACTERISTICA
        WHERE
            DESCRIPCION_CARACTERISTICA = 'SUBRED_PRIVADA' AND ESTADO = 'Activo'
    ),
        SYSDATE,
        NULL,
        'ajvalencia',
        NULL,
        'Activo',
        'NO'
);

INSERT INTO DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA
    (
    ID_PRODUCTO_CARACTERISITICA,
    PRODUCTO_ID,
    CARACTERISTICA_ID,
    FE_CREACION,
    FE_ULT_MOD,
    USR_CREACION,
    USR_ULT_MOD,
    ESTADO,
    VISIBLE_COMERCIAL
    )
VALUES
    (
        DB_COMERCIAL.SEQ_ADMI_PRODUCTO_CARAC.NEXTVAL,
        (
        SELECT
            ID_PRODUCTO
        FROM
            DB_COMERCIAL.ADMI_PRODUCTO
        WHERE
            DESCRIPCION_PRODUCTO = 'CLEAR CHANNEL PUNTO A PUNTO' AND ESTADO = 'Activo'
    ),
        (
        SELECT
            ID_CARACTERISTICA
        FROM
            DB_COMERCIAL.ADMI_CARACTERISTICA
        WHERE
            DESCRIPCION_CARACTERISTICA = 'ES_BACKUP' AND ESTADO = 'Activo'
    ),
        SYSDATE,
        NULL,
        'ajvalencia',
        NULL,
        'Activo',
        'NO'
);

INSERT INTO DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA
    (
    ID_PRODUCTO_CARACTERISITICA,
    PRODUCTO_ID,
    CARACTERISTICA_ID,
    FE_CREACION,
    FE_ULT_MOD,
    USR_CREACION,
    USR_ULT_MOD,
    ESTADO,
    VISIBLE_COMERCIAL
    )
VALUES
    (
        DB_COMERCIAL.SEQ_ADMI_PRODUCTO_CARAC.NEXTVAL,
        (
        SELECT
            ID_PRODUCTO
        FROM
            DB_COMERCIAL.ADMI_PRODUCTO
        WHERE
            DESCRIPCION_PRODUCTO = 'CLEAR CHANNEL PUNTO A PUNTO' AND ESTADO = 'Activo'
    ),
        (
        SELECT
            ID_CARACTERISTICA
        FROM
            DB_COMERCIAL.ADMI_CARACTERISTICA
        WHERE
            DESCRIPCION_CARACTERISTICA = 'REQUIERE_BACKUP' AND ESTADO = 'Activo'
    ),
        SYSDATE,
        NULL,
        'ajvalencia',
        NULL,
        'Activo',
        'NO'
);

INSERT INTO DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA
    (
    ID_PRODUCTO_CARACTERISITICA,
    PRODUCTO_ID,
    CARACTERISTICA_ID,
    FE_CREACION,
    FE_ULT_MOD,
    USR_CREACION,
    USR_ULT_MOD,
    ESTADO,
    VISIBLE_COMERCIAL
    )
VALUES
    (
        DB_COMERCIAL.SEQ_ADMI_PRODUCTO_CARAC.NEXTVAL,
        (
        SELECT
            ID_PRODUCTO
        FROM
            DB_COMERCIAL.ADMI_PRODUCTO
        WHERE
            DESCRIPCION_PRODUCTO = 'CLEAR CHANNEL PUNTO A PUNTO' AND ESTADO = 'Activo'
    ),
        (
        SELECT
            ID_CARACTERISTICA
        FROM
            DB_COMERCIAL.ADMI_CARACTERISTICA
        WHERE
            DESCRIPCION_CARACTERISTICA = 'VLAN' AND ESTADO = 'Activo'
    ),
        SYSDATE,
        NULL,
        'ajvalencia',
        NULL,
        'Activo',
        'NO'
);


INSERT INTO DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA
    (
    ID_PRODUCTO_CARACTERISITICA,
    PRODUCTO_ID,
    CARACTERISTICA_ID,
    FE_CREACION,
    FE_ULT_MOD,
    USR_CREACION,
    USR_ULT_MOD,
    ESTADO,
    VISIBLE_COMERCIAL
    )
VALUES
    (
        DB_COMERCIAL.SEQ_ADMI_PRODUCTO_CARAC.NEXTVAL,
        (
        SELECT
            ID_PRODUCTO
        FROM
            DB_COMERCIAL.ADMI_PRODUCTO
        WHERE
            DESCRIPCION_PRODUCTO = 'CLEAR CHANNEL PUNTO A PUNTO' AND ESTADO = 'Activo'
    ),
        (
        SELECT
            ID_CARACTERISTICA
        FROM
            DB_COMERCIAL.ADMI_CARACTERISTICA
        WHERE
            DESCRIPCION_CARACTERISTICA = 'MODELO BACKUP' AND ESTADO = 'Activo'
    ),
        SYSDATE,
        NULL,
        'ajvalencia',
        NULL,
        'Activo',
        'NO'
);


INSERT INTO DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA
    (
    ID_PRODUCTO_CARACTERISITICA,
    PRODUCTO_ID,
    CARACTERISTICA_ID,
    FE_CREACION,
    FE_ULT_MOD,
    USR_CREACION,
    USR_ULT_MOD,
    ESTADO,
    VISIBLE_COMERCIAL
    )
VALUES
    (
        DB_COMERCIAL.SEQ_ADMI_PRODUCTO_CARAC.NEXTVAL,
        (
        SELECT
            ID_PRODUCTO
        FROM
            DB_COMERCIAL.ADMI_PRODUCTO
        WHERE
            DESCRIPCION_PRODUCTO = 'CLEAR CHANNEL PUNTO A PUNTO' AND ESTADO = 'Activo'
    ),
        (
        SELECT
            ID_CARACTERISTICA
        FROM
            DB_COMERCIAL.ADMI_CARACTERISTICA
        WHERE
            DESCRIPCION_CARACTERISTICA = 'INTERFACE_EQUIPO' AND ESTADO = 'Activo'
    ),
        SYSDATE,
        NULL,
        'ajvalencia',
        NULL,
        'Activo',
        'NO'
);

INSERT INTO DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA
    (
    ID_PRODUCTO_CARACTERISITICA,
    PRODUCTO_ID,
    CARACTERISTICA_ID,
    FE_CREACION,
    FE_ULT_MOD,
    USR_CREACION,
    USR_ULT_MOD,
    ESTADO,
    VISIBLE_COMERCIAL
    )
VALUES
    (
        DB_COMERCIAL.SEQ_ADMI_PRODUCTO_CARAC.NEXTVAL,
        (
        SELECT
            ID_PRODUCTO
        FROM
            DB_COMERCIAL.ADMI_PRODUCTO
        WHERE
            DESCRIPCION_PRODUCTO = 'CLEAR CHANNEL PUNTO A PUNTO' AND ESTADO = 'Activo'
    ),
        (
        SELECT
            ID_CARACTERISTICA
        FROM
            DB_COMERCIAL.ADMI_CARACTERISTICA
        WHERE
            DESCRIPCION_CARACTERISTICA = 'SERIE_EQUIPO' AND ESTADO = 'Activo'
    ),
        SYSDATE,
        NULL,
        'ajvalencia',
        NULL,
        'Activo',
        'NO'
);


INSERT INTO DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA
    (
    ID_PRODUCTO_CARACTERISITICA,
    PRODUCTO_ID,
    CARACTERISTICA_ID,
    FE_CREACION,
    FE_ULT_MOD,
    USR_CREACION,
    USR_ULT_MOD,
    ESTADO,
    VISIBLE_COMERCIAL
    )
VALUES
    (
        DB_COMERCIAL.SEQ_ADMI_PRODUCTO_CARAC.NEXTVAL,
        (
        SELECT
            ID_PRODUCTO
        FROM
            DB_COMERCIAL.ADMI_PRODUCTO
        WHERE
            DESCRIPCION_PRODUCTO = 'CLEAR CHANNEL PUNTO A PUNTO' AND ESTADO = 'Activo'
    ),
        (
        SELECT
            ID_CARACTERISTICA
        FROM
            DB_COMERCIAL.ADMI_CARACTERISTICA
        WHERE
            DESCRIPCION_CARACTERISTICA = 'MODELO_EQUIPO' AND ESTADO = 'Activo'
    ),
        SYSDATE,
        NULL,
        'ajvalencia',
        NULL,
        'Activo',
        'NO'
);


INSERT INTO DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA
    (
    ID_PRODUCTO_CARACTERISITICA,
    PRODUCTO_ID,
    CARACTERISTICA_ID,
    FE_CREACION,
    FE_ULT_MOD,
    USR_CREACION,
    USR_ULT_MOD,
    ESTADO,
    VISIBLE_COMERCIAL
    )
VALUES
    (
        DB_COMERCIAL.SEQ_ADMI_PRODUCTO_CARAC.NEXTVAL,
        (
        SELECT
            ID_PRODUCTO
        FROM
            DB_COMERCIAL.ADMI_PRODUCTO
        WHERE
            DESCRIPCION_PRODUCTO = 'CLEAR CHANNEL PUNTO A PUNTO' AND ESTADO = 'Activo'
    ),
        (
        SELECT
            ID_CARACTERISTICA
        FROM
            DB_COMERCIAL.ADMI_CARACTERISTICA
        WHERE
            DESCRIPCION_CARACTERISTICA = 'IP_EQUIPO' AND ESTADO = 'Activo'
    ),
        SYSDATE,
        NULL,
        'ajvalencia',
        NULL,
        'Activo',
        'NO'
);


INSERT INTO DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA
    (
    ID_PRODUCTO_CARACTERISITICA,
    PRODUCTO_ID,
    CARACTERISTICA_ID,
    FE_CREACION,
    FE_ULT_MOD,
    USR_CREACION,
    USR_ULT_MOD,
    ESTADO,
    VISIBLE_COMERCIAL
    )
VALUES
    (
        DB_COMERCIAL.SEQ_ADMI_PRODUCTO_CARAC.NEXTVAL,
        (
        SELECT
            ID_PRODUCTO
        FROM
            DB_COMERCIAL.ADMI_PRODUCTO
        WHERE
            DESCRIPCION_PRODUCTO = 'CLEAR CHANNEL PUNTO A PUNTO' AND ESTADO = 'Activo'
    ),
        (
        SELECT
            ID_CARACTERISTICA
        FROM
            DB_COMERCIAL.ADMI_CARACTERISTICA
        WHERE
            DESCRIPCION_CARACTERISTICA = 'DESCRIPCION_EQUIPO' AND ESTADO = 'Activo'
    ),
        SYSDATE,
        NULL,
        'ajvalencia',
        NULL,
        'Activo',
        'NO'
);


INSERT INTO DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA
    (
    ID_PRODUCTO_CARACTERISITICA,
    PRODUCTO_ID,
    CARACTERISTICA_ID,
    FE_CREACION,
    FE_ULT_MOD,
    USR_CREACION,
    USR_ULT_MOD,
    ESTADO,
    VISIBLE_COMERCIAL
    )
VALUES
    (
        DB_COMERCIAL.SEQ_ADMI_PRODUCTO_CARAC.NEXTVAL,
        (
        SELECT
            ID_PRODUCTO
        FROM
            DB_COMERCIAL.ADMI_PRODUCTO
        WHERE
            DESCRIPCION_PRODUCTO = 'CLEAR CHANNEL PUNTO A PUNTO' AND ESTADO = 'Activo'
    ),
        (
        SELECT
            ID_CARACTERISTICA
        FROM
            DB_COMERCIAL.ADMI_CARACTERISTICA
        WHERE
            DESCRIPCION_CARACTERISTICA = 'MAC' AND ESTADO = 'Activo'
    ),
        SYSDATE,
        NULL,
        'ajvalencia',
        NULL,
        'Activo',
        'NO'
);


INSERT INTO DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA
    (
    ID_PRODUCTO_CARACTERISITICA,
    PRODUCTO_ID,
    CARACTERISTICA_ID,
    FE_CREACION,
    FE_ULT_MOD,
    USR_CREACION,
    USR_ULT_MOD,
    ESTADO,
    VISIBLE_COMERCIAL
    )
VALUES
    (
        DB_COMERCIAL.SEQ_ADMI_PRODUCTO_CARAC.NEXTVAL,
        (
        SELECT
            ID_PRODUCTO
        FROM
            DB_COMERCIAL.ADMI_PRODUCTO
        WHERE
            DESCRIPCION_PRODUCTO = 'CLEAR CHANNEL PUNTO A PUNTO' AND ESTADO = 'Activo'
    ),
        (
        SELECT
            ID_CARACTERISTICA
        FROM
            DB_COMERCIAL.ADMI_CARACTERISTICA
        WHERE
            DESCRIPCION_CARACTERISTICA = 'PROPIETARIO DEL EQUIPO' AND ESTADO = 'Activo'
    ),
        SYSDATE,
        NULL,
        'ajvalencia',
        NULL,
        'Activo',
        'NO'
);


-- UPDATE ADMI_PRODUCTO_CARACTERISTICA
UPDATE DB_COMERCIAL.ADMI_PRODUCTO SET REQUIERE_INFO_TECNICA = 'SI', NOMBRE_TECNICO = 'INTERNET',
CLASIFICACION = 'INTERNET'
WHERE DESCRIPCION_PRODUCTO = 'CLEAR CHANNEL PUNTO A PUNTO' AND ESTADO = 'Activo';

COMMIT;

/
