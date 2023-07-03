/**
 * @author Luis Cabrera <lcabrera@telconet.ec>
 * @version 1.0 06-12-2017 Se actualiza el código de los CICLOS ya creados.
 */
    UPDATE DB_FINANCIERO.ADMI_CICLO
        SET CODIGO = 'CICLO1'
        WHERE
            NOMBRE_CICLO LIKE '%Ciclo (I) - 1 al 30%'
            AND EMPRESA_COD = '18';

    UPDATE DB_FINANCIERO.ADMI_CICLO
        SET CODIGO = 'CICLO2'
        WHERE
            NOMBRE_CICLO LIKE '%Ciclo (II) - 15 al 14%'
            AND EMPRESA_COD = '18';

    COMMIT;