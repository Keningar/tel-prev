-- Creacion de nuevo tipo de solicitud para cableado ethernet
INSERT INTO db_comercial.admi_tipo_solicitud
(
    ID_TIPO_SOLICITUD,
    DESCRIPCION_SOLICITUD,
    FE_CREACION,
    USR_CREACION,
    FE_ULT_MOD,
    USR_ULT_MOD,
    ESTADO,
    TAREA_ID,
    ITEM_MENU_ID,
    PROCESO_ID
)
VALUES
(
    db_comercial.SEQ_ADMI_TIPO_SOLICITUD.NEXTVAL,
    'SOLICITUD DE INSTALACION CABLEADO ETHERNET',
    sysdate,
    'djreyes',
    null,
    null,
    'Activo',
    null,
    null,
    null
);

COMMIT;
/