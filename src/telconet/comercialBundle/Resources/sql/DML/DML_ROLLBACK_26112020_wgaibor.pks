DELETE FROM db_general.admi_parametro_det
WHERE
    parametro_id = (
        SELECT
            id_parametro
        FROM
            db_general.admi_parametro_cab
        WHERE
            nombre_parametro = 'ESTADO_AUTORIZADO_CONTRATO_ADENDUM'
            AND estado = 'Activo'
    );

DELETE FROM db_general.admi_parametro_cab
WHERE
    nombre_parametro = 'ESTADO_AUTORIZADO_CONTRATO_ADENDUM'
    AND estado = 'Activo';

COMMIT;
/
