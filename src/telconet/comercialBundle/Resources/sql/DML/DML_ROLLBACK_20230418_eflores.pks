-- REVERSO DE LA CABECERA Y DETALLES DEL PARAMETROS DE 'N_DIAS_PRECEDENTES'
DELETE DB_GENERAL.ADMI_PARAMETRO_DET
WHERE
    PARAMETRO_ID = (
        SELECT
            ID_PARAMETRO
        FROM
            DB_GENERAL.ADMI_PARAMETRO_CAB
        WHERE
            NOMBRE_PARAMETRO = 'N_DIAS_PRECEDENTES'
    );
DELETE DB_GENERAL.ADMI_PARAMETRO_CAB
WHERE
    NOMBRE_PARAMETRO = 'N_DIAS_PRECEDENTES';

COMMIT;
/