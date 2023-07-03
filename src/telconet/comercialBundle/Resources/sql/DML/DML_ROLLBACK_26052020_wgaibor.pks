DELETE FROM db_general.admi_parametro_det
WHERE
    parametro_id = (
        SELECT
            id_parametro
        FROM
            db_general.admi_parametro_cab
        WHERE
            nombre_parametro = 'REGULARIZACION_CONTRATO_TM_COMERCIAL'
            AND estado = 'Activo'
    );

DELETE FROM db_general.admi_parametro_cab
WHERE
    nombre_parametro = 'REGULARIZACION_CONTRATO_TM_COMERCIAL'
    AND estado = 'Activo';

COMMIT;
/