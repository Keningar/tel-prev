
DELETE FROM DB_GENERAL.ADMI_PARAMETRO_DET DETALLE
WHERE
    PARAMETRO_ID = (
        SELECT
            ID_PARAMETRO
        FROM
            DB_GENERAL.ADMI_PARAMETRO_CAB
        WHERE
            NOMBRE_PARAMETRO = 'REPORTE_CARTERA'
    )
    AND VALOR1 = 'URL_NFS';

COMMIT;

