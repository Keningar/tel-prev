/**
 * Script que asigna el ciclo a los clientes creados en producción sin ciclo. Se asignan al ciclo 1.
 * @author Luis Cabrera <lcabrera@telconet.ec>
 * @version 1.0
 * @since 24-04-2018
 */
DECLARE
    CURSOR C_ClientesSinCiclo (Cn_IdCaracteristica NUMBER) IS
        SELECT
            PER2.ID_PERSONA_ROL, PER2.ESTADO
        FROM
            DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL PER2,
            DB_COMERCIAL.INFO_EMPRESA_ROL ER2,
            DB_GENERAL.ADMI_ROL R2
        WHERE
            PER2.EMPRESA_ROL_ID = ER2.ID_EMPRESA_ROL
            AND   ER2.EMPRESA_COD = '18'
            AND   ER2.ROL_ID = R2.ID_ROL
            AND   R2.DESCRIPCION_ROL = 'Cliente'
            AND   PER2.FE_CREACION >= TO_DATE('15/04/2018','DD/MM/YYYY')
            AND   PER2.ID_PERSONA_ROL NOT IN (
                SELECT
                    PER.ID_PERSONA_ROL
                FROM
                    DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL PER
                    LEFT JOIN DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC PERC ON PER.ID_PERSONA_ROL = PERC.PERSONA_EMPRESA_ROL_ID,
                    DB_COMERCIAL.INFO_EMPRESA_ROL ER,
                    DB_GENERAL.ADMI_ROL R
                WHERE
                    PER.EMPRESA_ROL_ID = ER.ID_EMPRESA_ROL
                    AND   ER.EMPRESA_COD = '18'
                    AND   ER.ROL_ID = R.ID_ROL
                    AND   R.DESCRIPCION_ROL = 'Cliente'
                    AND   PERC.CARACTERISTICA_ID = Cn_IdCaracteristica
                    AND   PER.FE_CREACION >= TO_DATE('15/04/2018','DD/MM/YYYY')
            );

        Ln_IdCaracteristica NUMBER := 0;
        Le_Error            EXCEPTION;
        Lv_UsrCreacion      VARCHAR2(15) := 'regulaCiclo2';
BEGIN

    SELECT ID_CARACTERISTICA INTO Ln_IdCaracteristica
      FROM DB_COMERCIAL.ADMI_CARACTERISTICA
     WHERE DESCRIPCION_CARACTERISTICA = 'CICLO_FACTURACION'
       AND ESTADO = 'Activo';

    IF Ln_IdCaracteristica = 0 THEN
        RAISE Le_Error;
    END IF;

    FOR Cr_Clientes IN C_ClientesSinCiclo (Ln_IdCaracteristica)
    LOOP
        --Inserto la característica del ciclo
        INSERT INTO
                DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC (
                    ID_PERSONA_EMPRESA_ROL_CARACT,
                    PERSONA_EMPRESA_ROL_ID,
                    CARACTERISTICA_ID,
                    VALOR,
                    FE_CREACION,
                    FE_ULT_MOD,
                    USR_CREACION,
                    USR_ULT_MOD,
                    IP_CREACION,
                    ESTADO,
                    PERSONA_EMPRESA_ROL_CARAC_ID
                ) VALUES (
                    DB_COMERCIAL.SEQ_INFO_PERSONA_EMP_ROL_CARAC.NEXTVAL,
                    Cr_Clientes.ID_PERSONA_ROL,
                    Ln_IdCaracteristica,
                    5,
                    SYSDATE,
                    NULL,
                    Lv_UsrCreacion,
                    NULL,
                    '127.0.0.1',
                    Cr_Clientes.ESTADO,
                    NULL
                );

        --Inserto el historial de la persona
        INSERT INTO
                DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_HISTO (
                    ID_PERSONA_EMPRESA_ROL_HISTO,
                    USR_CREACION,
                    FE_CREACION,
                    IP_CREACION,
                    ESTADO,
                    PERSONA_EMPRESA_ROL_ID,
                    OBSERVACION,
                    MOTIVO_ID,
                    EMPRESA_ROL_ID,
                    OFICINA_ID,
                    DEPARTAMENTO_ID,
                    CUADRILLA_ID,
                    REPORTA_PERSONA_EMPRESA_ROL_ID,
                    ES_PREPAGO
                ) VALUES (
                    DB_COMERCIAL.SEQ_INFO_PERSONA_EMPRESA_ROL_H.NEXTVAL,
                    Lv_UsrCreacion,
                    SYSDATE,
                    '127.0.0.1',
                    Cr_Clientes.ESTADO,
                    Cr_Clientes.ID_PERSONA_ROL,
                    'Se asigna el ciclo por regularización: Ciclo (I) - 1 al 30',
                    NULL,
                    NULL,
                    NULL,
                    NULL,
                    NULL,
                    NULL,
                    NULL
                );
    END LOOP;
    COMMIT;
    DBMS_OUTPUT.PUT_LINE('SE EJECUTÓ CORRECTAMENTE EL SCRIPT DE REGULARIZACIÓN.');
EXCEPTION
    WHEN Le_Error THEN
        DBMS_OUTPUT.PUT_LINE('OCURRIÓ UN ERROR AL BUSCAR LA CARACTERÍSTICA DEL CICLO DE FACTURACIÓN.');
    WHEN OTHERS THEN
        DBMS_OUTPUT.PUT_LINE('OCURRIÓ UN ERROR AL PROCESAR LA REGULARIZACIÓN.');
        ROLLBACK;
END;
