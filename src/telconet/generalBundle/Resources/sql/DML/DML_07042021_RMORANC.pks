--Estados de tarea utilizados en validación cuando supera el primer umbral de actualización de coordenadas

INSERT INTO db_general.admi_parametro_det VALUES 
(
    db_general.seq_admi_parametro_det.NEXTVAL,
    (
        SELECT
            id_parametro
        FROM
            db_general.admi_parametro_cab
        WHERE
            nombre_parametro = 'PARAMETROS_ACTUALIZACION_COORDENADAS_MOVIL'
    ),
    'Estados de tarea utilizados en validación cuando supera el primer umbral job de actualización',
    'ESTADOS_TAREA_MAYOR_UMBRAL_UNO',
    'Ninguno', 
    NULL,
    NULL,
    'Activo',
    'rmoranc',
    SYSDATE,
    '127.0.0.1', 
    NULL,
    NULL,
    NULL,
    NULL,
    NULL,
    NULL,
    NULL,
    NULL
);
    

COMMIT ;
/