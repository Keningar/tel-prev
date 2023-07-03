/*
 * INSERT de la característica para facturar los servicios facturados en otro ciclo por CRS.
 * @author Luis Cabrera <lcabrera@telconet.ec>
 * @version 1.0
 * @since 07-06-2018
 */
INSERT INTO DB_COMERCIAL.ADMI_CARACTERISTICA (
    ID_CARACTERISTICA,
    DESCRIPCION_CARACTERISTICA,
    TIPO_INGRESO,
    ESTADO,
    FE_CREACION,
    USR_CREACION,
    FE_ULT_MOD,
    USR_ULT_MOD,
    TIPO
) VALUES (
    DB_COMERCIAL.SEQ_ADMI_CARACTERISTICA.NEXTVAL,
    'FACTURACION_CRS_CICLO_FACT',
    'T',
    'Activo',
    SYSDATE,
    'lcabrera',
    NULL,
    NULL,
    'COMERCIAL'
);

COMMIT;